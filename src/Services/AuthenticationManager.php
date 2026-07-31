<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Moodlood\LaravelDaraja\Contracts\AuthenticatorInterface;
use Moodlood\LaravelDaraja\DTOs\AccessToken;
use Moodlood\LaravelDaraja\Exceptions\AuthenticationException;
use Moodlood\LaravelDaraja\Support\Config;

/**
 * Manages OAuth 2.0 authentication with the Daraja API.
 *
 * Automatically caches tokens and refreshes them before expiry.
 * Supports separate credential sets for different API contexts (C2B vs B2C).
 */
final class AuthenticationManager implements AuthenticatorInterface
{
    public function __construct(
        private readonly Config $config,
    ) {}

    /**
     * Get a valid access token, retrieving from cache or generating a fresh one.
     *
     * @throws AuthenticationException
     */
    public function getToken(?string $consumerKey = null, ?string $consumerSecret = null): string
    {
        $consumerKey ??= $this->config->consumerKey();
        $consumerSecret ??= $this->config->consumerSecret();

        $cacheKey = $this->cacheKey($consumerKey);

        /** @var AccessToken|null $cached */
        $cached = Cache::store($this->config->cacheStore())->get($cacheKey);

        if ($cached instanceof AccessToken && ! $cached->isExpired()) {
            return $cached->token;
        }

        return $this->generateAndCacheToken($consumerKey, $consumerSecret, $cacheKey);
    }

    /**
     * Clear the cached access token.
     */
    public function clearToken(?string $consumerKey = null): void
    {
        $consumerKey ??= $this->config->consumerKey();
        $cacheKey = $this->cacheKey($consumerKey);

        Cache::store($this->config->cacheStore())->forget($cacheKey);
    }

    /**
     * Generate a fresh token from the Daraja OAuth endpoint and cache it.
     *
     * @throws AuthenticationException
     */
    private function generateAndCacheToken(string $consumerKey, string $consumerSecret, string $cacheKey): string
    {
        $url = $this->config->baseUrl().'/oauth/v1/generate?grant_type=client_credentials';

        try {
            $response = Http::withBasicAuth($consumerKey, $consumerSecret)
                ->timeout($this->config->httpTimeout())
                ->connectTimeout($this->config->httpConnectTimeout())
                ->acceptJson()
                ->get($url);

            if ($response->failed()) {
                throw new AuthenticationException(
                    "Failed to obtain OAuth token from Safaricom API.\n\n"
                    ."Possible causes:\n"
                    ."- Invalid Consumer Key or Secret.\n"
                    ."- Sandbox credentials used in Production environment (or vice versa).\n"
                    ."- Network timeout or Safaricom API downtime.\n\n"
                    ."HTTP Status: {$response->status()}\n"
                    ."Response: {$response->body()}"
                );
            }

            /** @var array<string, mixed> $data */
            $data = $response->json();

            if (! isset($data['access_token'])) {
                throw new AuthenticationException(
                    "M-Pesa OAuth response did not contain an access token.\n"
                    ."This usually happens if Daraja returns an unexpected success format.\n"
                    .'Response: '.json_encode($data, JSON_THROW_ON_ERROR),
                );
            }

            $accessToken = AccessToken::fromResponse($data, $this->config->cacheTtlBuffer());

            Cache::store($this->config->cacheStore())
                ->put($cacheKey, $accessToken, $accessToken->ttlSeconds());

            return $accessToken->token;

        } catch (AuthenticationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new AuthenticationException(
                "Failed to authenticate with M-Pesa API: {$e->getMessage()}",
                previous: $e,
            );
        }
    }

    /**
     * Generate a unique cache key for a consumer key.
     */
    private function cacheKey(string $consumerKey): string
    {
        return $this->config->cachePrefix().'token_'.md5($consumerKey);
    }
}

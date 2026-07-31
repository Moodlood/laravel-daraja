<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Http;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Moodlood\LaravelDaraja\Contracts\AuthenticatorInterface;
use Moodlood\LaravelDaraja\Contracts\MpesaClientInterface;
use Moodlood\LaravelDaraja\Events\TransactionInitiated;
use Moodlood\LaravelDaraja\Exceptions\NetworkException;
use Moodlood\LaravelDaraja\Exceptions\TimeoutException;
use Moodlood\LaravelDaraja\Support\Config;

/**
 * HTTP client for communicating with the Daraja API.
 *
 * Handles authentication headers, retries with exponential backoff,
 * timeouts, and error mapping. Never logs secrets.
 */
final class MpesaClient implements MpesaClientInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly AuthenticatorInterface $authenticator,
    ) {}

    /**
     * Send an authenticated POST request to the Daraja API.
     *
     * @param  array<string, mixed>  $data
     */
    public function post(string $endpoint, array $data = []): MpesaResponse
    {
        return $this->request('post', $endpoint, $data);
    }

    /**
     * Send an authenticated GET request to the Daraja API.
     *
     * @param  array<string, mixed>  $query
     */
    public function get(string $endpoint, array $query = []): MpesaResponse
    {
        return $this->request('get', $endpoint, $query);
    }

    /**
     * Send an authenticated request with retry logic.
     *
     * @param  array<string, mixed>  $data
     */
    private function request(string $method, string $endpoint, array $data): MpesaResponse
    {
        $url = $this->config->baseUrl().$endpoint;
        $token = $this->authenticator->getToken();

        $this->logRequest($method, $endpoint, $data);

        try {
            $pendingRequest = Http::withToken($token)
                ->timeout($this->config->httpTimeout())
                ->connectTimeout($this->config->httpConnectTimeout())
                ->retry(
                    times: $this->config->httpRetries(),
                    sleepMilliseconds: $this->config->httpRetryDelay(),
                    when: fn (\Throwable $e): bool => $e instanceof ConnectionException,
                    throw: false,
                )
                ->acceptJson();

            /** @var Response $response */
            $response = $method === 'get'
                ? $pendingRequest->get($url, $data)
                : $pendingRequest->post($url, $data);

            $mpesaResponse = new MpesaResponse(
                statusCode: $response->status(),
                data: $response->json() ?? [],
                headers: $response->headers(),
            );

            event(new TransactionInitiated($endpoint, $data, $mpesaResponse));

            $this->logResponse($endpoint, $mpesaResponse);

            return $mpesaResponse;

        } catch (ConnectionException $e) {
            $this->logError($endpoint, $e);

            if (str_contains($e->getMessage(), 'timed out')) {
                throw new TimeoutException(
                    "Request to [{$endpoint}] timed out after {$this->config->httpTimeout()} seconds.",
                    previous: $e,
                );
            }

            throw new NetworkException(
                "Failed to connect to M-Pesa API at [{$endpoint}]: {$e->getMessage()}",
                previous: $e,
            );
        } catch (RequestException $e) {
            $this->logError($endpoint, $e);

            throw new NetworkException(
                "HTTP request to [{$endpoint}] failed: {$e->getMessage()}",
                previous: $e,
            );
        }
    }

    /**
     * Log the outgoing request (without secrets).
     *
     * @param  array<string, mixed>  $data
     */
    private function logRequest(string $method, string $endpoint, array $data): void
    {
        if (! $this->config->isDebug()) {
            return;
        }

        // Sanitize sensitive fields before logging
        $sanitized = $this->sanitizeData($data);

        Log::channel($this->config->logChannel())
            ->debug("Mpesa API Request: {$method} {$endpoint}", $sanitized);
    }

    /**
     * Log the API response.
     */
    private function logResponse(string $endpoint, MpesaResponse $response): void
    {
        if (! $this->config->isDebug()) {
            return;
        }

        Log::channel($this->config->logChannel())
            ->debug("Mpesa API Response: {$endpoint}", [
                'status' => $response->statusCode(),
                'successful' => $response->successful(),
                'data' => $this->sanitizeData($response->json()),
            ]);
    }

    /**
     * Log an error.
     */
    private function logError(string $endpoint, \Throwable $e): void
    {
        Log::channel($this->config->logChannel())
            ->error("Mpesa API Error: {$endpoint}", [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
    }

    /**
     * Remove sensitive fields from data before logging.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function sanitizeData(array $data): array
    {
        $sensitiveKeys = [
            'Password', 'SecurityCredential', 'password',
            'security_credential', 'access_token', 'AccessToken',
        ];

        foreach ($sensitiveKeys as $key) {
            if (isset($data[$key])) {
                $data[$key] = '***REDACTED***';
            }
        }

        return $data;
    }
}

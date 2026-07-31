<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Contracts;

/**
 * Contract for the authentication manager.
 *
 * Handles OAuth token generation, caching, and refresh.
 */
interface AuthenticatorInterface
{
    /**
     * Get a valid access token, generating or refreshing as needed.
     *
     * @param  string|null  $consumerKey  Override the default consumer key.
     * @param  string|null  $consumerSecret  Override the default consumer secret.
     */
    public function getToken(?string $consumerKey = null, ?string $consumerSecret = null): string;

    /**
     * Clear the cached access token, forcing a fresh token on next request.
     */
    public function clearToken(?string $consumerKey = null): void;
}

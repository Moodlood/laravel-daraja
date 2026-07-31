<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Support;

use Moodlood\LaravelDaraja\Enums\Environment;
use Moodlood\LaravelDaraja\Exceptions\ConfigurationException;

/**
 * Typed accessor for package configuration values.
 *
 * Centralizes all config reads and validates required values,
 * throwing clear exceptions for missing configuration.
 */
final class Config
{
    /**
     * Get the active Daraja environment.
     */
    public function environment(): Environment
    {
        $env = config('mpesa.environment', 'sandbox');

        return Environment::from($env);
    }

    /**
     * Get the base URL for the active environment.
     */
    public function baseUrl(): string
    {
        return $this->environment()->baseUrl();
    }

    /**
     * Get the consumer key.
     *
     * @throws ConfigurationException
     */
    public function consumerKey(): string
    {
        return $this->requireString('mpesa.consumer_key', 'MPESA_CONSUMER_KEY');
    }

    /**
     * Get the consumer secret.
     *
     * @throws ConfigurationException
     */
    public function consumerSecret(): string
    {
        return $this->requireString('mpesa.consumer_secret', 'MPESA_CONSUMER_SECRET');
    }

    /**
     * Get the STK Push passkey.
     *
     * @throws ConfigurationException
     */
    public function passkey(): string
    {
        return $this->requireString('mpesa.passkey', 'MPESA_PASSKEY');
    }

    /**
     * Get the business shortcode.
     *
     * @throws ConfigurationException
     */
    public function shortcode(): string
    {
        return $this->requireString('mpesa.shortcode', 'MPESA_SHORTCODE');
    }

    /**
     * Get the till number, falling back to shortcode.
     */
    public function tillNumber(): string
    {
        $till = config('mpesa.till_number');

        if (is_string($till) && $till !== '') {
            return $till;
        }

        return $this->shortcode();
    }

    /**
     * Get B2C configuration value.
     *
     * @throws ConfigurationException
     */
    public function b2cConsumerKey(): string
    {
        return $this->requireString('mpesa.b2c.consumer_key', 'MPESA_B2C_CONSUMER_KEY');
    }

    public function b2cConsumerSecret(): string
    {
        return $this->requireString('mpesa.b2c.consumer_secret', 'MPESA_B2C_CONSUMER_SECRET');
    }

    public function b2cShortcode(): string
    {
        return $this->requireString('mpesa.b2c.shortcode', 'MPESA_B2C_SHORTCODE');
    }

    public function b2cInitiatorName(): string
    {
        return $this->requireString('mpesa.b2c.initiator_name', 'MPESA_INITIATOR_NAME');
    }

    public function b2cSecurityCredential(): string
    {
        return $this->requireString('mpesa.b2c.security_credential', 'MPESA_SECURITY_CREDENTIAL');
    }

    /**
     * Get B2B configuration value.
     *
     * @throws ConfigurationException
     */
    public function b2bConsumerKey(): string
    {
        return $this->requireString('mpesa.b2b.consumer_key', 'MPESA_B2B_CONSUMER_KEY');
    }

    public function b2bConsumerSecret(): string
    {
        return $this->requireString('mpesa.b2b.consumer_secret', 'MPESA_B2B_CONSUMER_SECRET');
    }

    public function b2bShortcode(): string
    {
        return $this->requireString('mpesa.b2b.shortcode', 'MPESA_B2B_SHORTCODE');
    }

    public function b2bInitiatorName(): string
    {
        return $this->requireString('mpesa.b2b.initiator_name', 'MPESA_B2B_INITIATOR_NAME');
    }

    public function b2bSecurityCredential(): string
    {
        return $this->requireString('mpesa.b2b.security_credential', 'MPESA_B2B_SECURITY_CREDENTIAL');
    }

    /**
     * Get a callback URL by key.
     */
    public function callbackUrl(string $key): ?string
    {
        $value = config("mpesa.callbacks.{$key}");

        return is_string($value) && $value !== '' ? $value : null;
    }

    /**
     * Get the cache store name.
     */
    public function cacheStore(): ?string
    {
        $store = config('mpesa.cache.store');

        return is_string($store) && $store !== '' ? $store : null;
    }

    /**
     * Get the cache key prefix.
     */
    public function cachePrefix(): string
    {
        return (string) config('mpesa.cache.prefix', 'mpesa_');
    }

    /**
     * Get the token TTL buffer in seconds.
     */
    public function cacheTtlBuffer(): int
    {
        return (int) config('mpesa.cache.ttl_buffer', 30);
    }

    /**
     * Get the HTTP timeout in seconds.
     */
    public function httpTimeout(): int
    {
        return (int) config('mpesa.http.timeout', 30);
    }

    /**
     * Get the HTTP connect timeout in seconds.
     */
    public function httpConnectTimeout(): int
    {
        return (int) config('mpesa.http.connect_timeout', 10);
    }

    /**
     * Get the number of HTTP retries.
     */
    public function httpRetries(): int
    {
        return (int) config('mpesa.http.retries', 3);
    }

    /**
     * Get the retry delay in milliseconds.
     */
    public function httpRetryDelay(): int
    {
        return (int) config('mpesa.http.retry_delay', 100);
    }

    /**
     * Get the log channel name.
     */
    public function logChannel(): ?string
    {
        $channel = config('mpesa.log_channel');

        return is_string($channel) && $channel !== '' ? $channel : null;
    }

    /**
     * Check if debug mode is enabled.
     */
    public function isDebug(): bool
    {
        return (bool) config('mpesa.debug', false);
    }

    /**
     * Get the webhook route prefix.
     */
    public function webhookPrefix(): string
    {
        return (string) config('mpesa.webhooks.prefix', 'api/mpesa/webhooks');
    }

    /**
     * Get the webhook middleware.
     *
     * @return array<int, string>
     */
    public function webhookMiddleware(): array
    {
        $middleware = config('mpesa.webhooks.middleware', ['api']);

        return is_array($middleware) ? $middleware : ['api'];
    }

    /**
     * Require a non-empty string config value.
     *
     * @throws ConfigurationException
     */
    private function requireString(string $key, string $envVar): string
    {
        $value = config($key);

        if (! is_string($value) || $value === '') {
            throw new ConfigurationException(
                "Missing required M-Pesa configuration: [{$key}]. "
                ."Set the {$envVar} environment variable in your .env file."
            );
        }

        return $value;
    }
}

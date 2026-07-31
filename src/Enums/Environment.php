<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Enums;

/**
 * Represents the Daraja API environment.
 *
 * Each environment has a distinct base URL for all API calls.
 */
enum Environment: string
{
    case Sandbox = 'sandbox';
    case Production = 'production';

    /**
     * Get the base URL for this environment.
     */
    public function baseUrl(): string
    {
        return match ($this) {
            self::Sandbox => 'https://sandbox.safaricom.co.ke',
            self::Production => 'https://api.safaricom.co.ke',
        };
    }
}

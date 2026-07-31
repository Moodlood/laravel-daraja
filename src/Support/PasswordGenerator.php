<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Support;

/**
 * Generates the password required for STK Push (Lipa Na M-Pesa Online) requests.
 *
 * Password = Base64Encode(BusinessShortCode + Passkey + Timestamp)
 */
final class PasswordGenerator
{
    /**
     * Generate the STK Push password.
     */
    public static function generate(string $shortcode, string $passkey, string $timestamp): string
    {
        return base64_encode($shortcode.$passkey.$timestamp);
    }
}

<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Support;

use Moodlood\LaravelDaraja\Exceptions\InvalidPhoneException;

/**
 * Normalizes Kenyan phone numbers to the format required by the Daraja API.
 *
 * Accepts formats: 07XX, +2547XX, 2547XX, 01XX, +2541XX
 * Outputs: 2547XXXXXXXX or 2541XXXXXXXX
 */
final class PhoneNormalizer
{
    /**
     * Normalize a phone number to Daraja-compatible format (2547XXXXXXXX).
     *
     * @throws InvalidPhoneException
     */
    public static function normalize(string $phone): string
    {
        // Remove all whitespace, dashes, and parentheses
        $phone = preg_replace('/[\s\-\(\)]+/', '', $phone) ?? $phone;

        // Remove leading + sign
        $phone = ltrim($phone, '+');

        // Convert 07XX to 2547XX
        if (preg_match('/^0([17]\d{8})$/', $phone, $matches)) {
            $phone = '254'.$matches[1];
        }

        // Validate final format: must be 2541XX or 2547XX (12 digits)
        if (! preg_match('/^254[17]\d{8}$/', $phone)) {
            throw new InvalidPhoneException(
                "Invalid Kenyan phone number: [{$phone}]. "
                .'Expected format: 07XXXXXXXX, +2547XXXXXXXX, or 2547XXXXXXXX.'
            );
        }

        return $phone;
    }
}

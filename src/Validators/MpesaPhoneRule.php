<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Validators;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Moodlood\LaravelDaraja\Support\PhoneNormalizer;

/**
 * Laravel validation rule for Kenyan M-Pesa phone numbers.
 *
 * Usage: new MpesaPhoneRule() or 'mpesa_phone'
 */
class MpesaPhoneRule implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || $value === '') {
            $fail('The :attribute must be a valid Kenyan phone number.');

            return;
        }

        try {
            PhoneNormalizer::normalize($value);
        } catch (\Throwable) {
            $fail('The :attribute must be a valid Kenyan phone number (e.g., 0712345678 or 254712345678).');
        }
    }
}

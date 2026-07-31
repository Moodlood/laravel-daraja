<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Validators;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Laravel validation rule for M-Pesa transaction amounts.
 *
 * Validates that the amount is a positive integer (M-Pesa does not support decimals).
 */
class MpesaAmountRule implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_numeric($value)) {
            $fail('The :attribute must be a numeric value.');

            return;
        }

        $amount = (int) $value;

        if ($amount <= 0) {
            $fail('The :attribute must be a positive amount.');

            return;
        }

        if ((float) $value !== (float) $amount) {
            $fail('The :attribute must be a whole number. M-Pesa does not support decimal amounts.');
        }
    }
}

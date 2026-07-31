# Reversal

The Reversal API allows you to reverse a successful B2B, B2C, or C2B M-Pesa transaction.

## Basic Usage

```php
use Moodlood\LaravelDaraja\Facades\Mpesa;

$response = Mpesa::reverse(
    transactionId: 'OEI2AK4Q16', // The M-Pesa Receipt Number to reverse
    amount: 1500, // Must match the exact amount of the original transaction
    remarks: 'Customer requested refund',
    occasion: 'Mistake' // Optional
);
```

## Callbacks

The reversal API is asynchronous. Safaricom will send the result to your configured `reversal_result` or `reversal_timeout` webhooks.

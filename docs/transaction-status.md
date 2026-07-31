# Transaction Status

The Transaction Status API allows you to check the status of a B2B, B2C, or C2B transaction.

## Basic Usage

```php
use Moodlood\LaravelDaraja\Facades\Mpesa;

$response = Mpesa::transactionStatus(
    transactionId: 'OEI2AK4Q16', // The M-Pesa Receipt Number
    identifierType: 4, // 4 for Shortcode (default)
    remarks: 'Checking status of payment'
);
```

## Callbacks

The status API is asynchronous. Safaricom will send the result to your configured `status_result` or `status_timeout` webhooks.

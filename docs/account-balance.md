# Account Balance

The Account Balance API allows you to check the balance of your PayBill or Buy Goods Till.

## Basic Usage

```php
use Moodlood\LaravelDaraja\Facades\Mpesa;

$response = Mpesa::balance(
    identifierType: 4, // 4 for Shortcode (default)
    remarks: 'Checking end of day balance'
);
```

## Callbacks

The balance API is asynchronous. Safaricom will send the result to your configured `balance_result` or `balance_timeout` webhooks.

# C2B (Customer to Business)

C2B APIs allow you to receive payments via PayBill or Till numbers.

## Registering URLs

Before receiving C2B payments, you must register your Validation and Confirmation URLs with Safaricom.

```php
use Moodlood\LaravelDaraja\Facades\Mpesa;

$response = Mpesa::c2bRegister(
    validationUrl: 'https://your-app.com/api/mpesa/webhooks/c2b/validation',
    confirmationUrl: 'https://your-app.com/api/mpesa/webhooks/c2b/confirmation',
    responseType: 'Completed' // or 'Cancelled'
);
```

> **Note:** The package automatically provides routes for these webhooks. You can use `route('mpesa.webhook.c2b-validation')` to generate the URLs dynamically.

## Simulating Payments (Sandbox Only)

In the Sandbox environment, you can simulate a C2B payment to test your webhooks:

```php
$response = Mpesa::c2bSimulate(
    phone: '0712345678', // The test phone number
    amount: 500,
    billRefNumber: 'INV-123' // Required for PayBill, ignored for Buy Goods
);
```

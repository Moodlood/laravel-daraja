# B2C (Business to Customer)

The B2C API allows a business to send money to a customer (e.g., salary payments, promotions, refunds).

## Basic Usage

```php
use Moodlood\LaravelDaraja\Facades\Mpesa;

$response = Mpesa::b2c(
    phone: '0712345678',
    amount: 1500,
    commandId: 'BusinessPayment', // Default
    remarks: 'Salary Payment',
    occasion: 'January 2024 Salary' // Optional
);

if ($response->successful()) {
    $conversationId = $response->conversationId();
}
```

## Command IDs

The `commandId` parameter determines the purpose of the payment:
- `BusinessPayment`: Normal business payments (default).
- `SalaryPayment`: Salary disbursements.
- `PromotionPayment`: Promotional disbursements.

## Callbacks

B2C transactions are asynchronous. The API will respond immediately with a `ConversationID`, but the actual success or failure is sent to your configured `b2c_result` or `b2c_timeout` webhook URLs.

Listen to the `B2CResultReceived` event to process the final outcome.

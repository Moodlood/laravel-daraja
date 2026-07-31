# Webhooks & Events

The Laravel Daraja package abstracts the complexity of Safaricom's asynchronous callbacks by automatically registering routes and firing Laravel Events when payloads are received.

## Auto-Registered Routes

By default, the package registers the following routes under the `api/mpesa/webhooks` prefix:

- `/stk-push`
- `/c2b/validation`
- `/c2b/confirmation`
- `/b2c/result`
- `/b2c/timeout`
- `/b2b/result`
- `/b2b/timeout`
- `/reversal/result`
- `/reversal/timeout`
- `/balance/result`
- `/balance/timeout`
- `/status/result`
- `/status/timeout`

You can change the prefix in `config/mpesa.php`.

## Listening to Events

Instead of writing controllers for these routes, you simply create Event Listeners in your application.

Register your listeners in your `EventServiceProvider`:

```php
use Moodlood\LaravelDaraja\Events\StkPushReceived;
use Moodlood\LaravelDaraja\Events\C2BPaymentReceived;

protected $listen = [
    StkPushReceived::class => [
        \App\Listeners\HandleStkPushCallback::class,
    ],
    C2BPaymentReceived::class => [
        \App\Listeners\HandleC2BPayment::class,
    ],
];
```

## Example Listener

```php
namespace App\Listeners;

use Moodlood\LaravelDaraja\Events\StkPushReceived;
use Illuminate\Support\Facades\Log;

class HandleStkPushCallback
{
    public function handle(StkPushReceived $event): void
    {
        $payload = $event->payload;
        
        $resultCode = $payload['Body']['stkCallback']['ResultCode'];
        $checkoutId = $payload['Body']['stkCallback']['CheckoutRequestID'];

        if ($resultCode === 0) {
            // Payment was successful
            Log::info("Payment {$checkoutId} successful!");
        } else {
            // Payment failed or was cancelled
            Log::warning("Payment {$checkoutId} failed: " . $payload['Body']['stkCallback']['ResultDesc']);
        }
    }
}
```

## Available Events

- `StkPushReceived`
- `C2BValidationReceived`
- `C2BPaymentReceived`
- `B2CResultReceived`
- `B2BResultReceived`
- `ReversalResultReceived`

## Transaction Logger

If you publish and run the package's migrations (`php artisan mpesa:install`), a `TransactionLogger` listener is automatically activated. It listens to all outgoing requests and incoming webhooks and logs them to the `mpesa_transactions` database table, providing a complete audit trail out of the box.

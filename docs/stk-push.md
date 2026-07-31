# STK Push (Lipa Na M-Pesa Online)

STK Push allows you to trigger a payment request directly on the customer's phone.

## Basic Usage

The package provides a fluent builder to construct STK Push requests:

```php
use Moodlood\LaravelDaraja\Facades\Mpesa;

$response = Mpesa::stkPush()
    ->phone('0712345678') // Or 254712345678
    ->amount(100)
    ->reference('INV-001')
    ->description('Payment for invoice')
    ->push();
```

## Buy Goods (Till Number)

By default, the STK Push uses the PayBill transaction type. If you are using a Till Number (Buy Goods), you must specify this:

```php
$response = Mpesa::stkPush()
    ->buyGoods()
    ->phone('0712345678')
    ->amount(100)
    ->reference('Order #123')
    ->description('Payment for order')
    ->push();
```

## Custom Callback URL

If you need to override the default callback URL configured in `config/mpesa.php` for a specific request:

```php
$response = Mpesa::stkPush()
    ->phone('0712345678')
    ->amount(100)
    ->reference('INV-001')
    ->callbackUrl('https://example.com/custom-webhook')
    ->push();
```

## Querying STK Push Status

You can query the status of an STK Push request using the `CheckoutRequestID` returned from the initial push:

```php
$checkoutId = $response->checkoutRequestId();

$statusResponse = Mpesa::stkQuery($checkoutId);

if ($statusResponse->successful()) {
    echo $statusResponse->get('ResultDesc');
}
```

# Dynamic QR Codes

The Dynamic QR API generates a QR code that a customer can scan with their M-Pesa App to initiate a payment.

## Basic Usage

```php
use Moodlood\LaravelDaraja\Facades\Mpesa;

$response = Mpesa::qr(
    merchantName: 'My Awesome Store',
    refNo: 'INV-12345',
    amount: 500,
    trxCode: 'PB', // PB (PayBill), BG (BuyGoods), WA (Withdraw), SM (SendMoney)
    size: 300 // QR code size in pixels (default: 300)
);

if ($response->successful()) {
    $qrCodeString = $response->get('QRCode'); // The generated QR Code string
}
```

## Transaction Codes (TrxCode)

- `PB`: PayBill
- `BG`: Buy Goods
- `WA`: Withdraw Cash at Agent Till
- `SM`: Send Money

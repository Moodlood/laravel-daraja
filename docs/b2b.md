# B2B (Business to Business)

The B2B API allows you to transfer funds from one business shortcode to another.

## Basic Usage

```php
use Moodlood\LaravelDaraja\Facades\Mpesa;
use Moodlood\LaravelDaraja\Enums\IdentifierType;

$response = Mpesa::b2b(
    receiverShortcode: '600000',
    amount: 50000,
    commandId: 'BusinessPayBill', // Default
    receiverIdentifierType: IdentifierType::Shortcode->value, // 4
    accountReference: 'Supplier Payment', // Required for PayBill
    remarks: 'Payment for Invoice #998'
);
```

## Command IDs

- `BusinessPayBill`: Sending funds to a PayBill number.
- `BusinessBuyGoods`: Sending funds to a Buy Goods Till number.
- `DisburseFundsToBusiness`: Transferring funds to another business shortcode.
- `BusinessToBusinessTransfer`: Standard B2B transfer.
- `MerchantToMerchantTransfer`: Transfer between merchants.

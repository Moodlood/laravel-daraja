# Laravel Daraja (M-Pesa SDK)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/moodlood/laravel-daraja.svg?style=flat-square)](https://packagist.org/packages/moodlood/laravel-daraja)
[![Total Downloads](https://img.shields.io/packagist/dt/moodlood/laravel-daraja.svg?style=flat-square)](https://packagist.org/packages/moodlood/laravel-daraja)
[![Tests](https://img.shields.io/github/actions/workflow/status/Moodlood/laravel-daraja/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/Moodlood/laravel-daraja/actions)
[![Coverage](https://img.shields.io/codecov/c/github/Moodlood/laravel-daraja?style=flat-square)](https://codecov.io/gh/Moodlood/laravel-daraja)
[![PHP Version Require](https://img.shields.io/packagist/php-v/moodlood/laravel-daraja?style=flat-square)](https://packagist.org/packages/moodlood/laravel-daraja)
[![Laravel Version Require](https://img.shields.io/static/v1?label=laravel&message=^11.0&color=red&style=flat-square)](https://laravel.com)
[![License](https://img.shields.io/packagist/l/moodlood/laravel-daraja?style=flat-square)](https://packagist.org/packages/moodlood/laravel-daraja)
[![Static Analysis](https://img.shields.io/badge/phpstan-level%208-brightgreen.svg?style=flat-square)](https://github.com/Moodlood/laravel-daraja/actions)
[![Code Style](https://img.shields.io/badge/code%20style-pint-brightgreen.svg?style=flat-square)](https://github.com/Moodlood/laravel-daraja/actions)

A clean, modern, and fully featured Laravel package for the **Safaricom Daraja M-Pesa API**. Built with strict typing, fluent builders, comprehensive testing, and excellent developer experience.

## Features

- 🚀 **Fluent Builder API** — Clean, chainable syntax for STK Push
- 🔐 **Automatic Auth** — OAuth tokens managed and cached transparently
- 📡 **All Daraja APIs** — STK Push, C2B, B2C, B2B, Balance, Status, Reversal, QR
- 🎯 **Webhook System** — Event-driven callbacks with auto-registered routes
- 🛡️ **Strict Typing** — DTOs, Enums, and typed config throughout
- ✅ **Tested** — Pest test suite with HTTP fakes
- 📦 **Zero Config** — Auto-discovery, just add your API keys
- 🔧 **Artisan Install** — `php artisan mpesa:install` setup wizard

## Installation

```bash
composer require moodlood/laravel-daraja
```

```bash
php artisan mpesa:install
```

## Quick Start

```php
use Moodlood\LaravelDaraja\Facades\Mpesa;

// STK Push (Lipa Na M-Pesa)
$response = Mpesa::stkPush()
    ->phone('0712345678')
    ->amount(100)
    ->reference('INV-001')
    ->description('Payment for invoice')
    ->push();

// Check response
if ($response->successful()) {
    $checkoutId = $response->checkoutRequestId();
}
```

## Configuration

Add your Daraja credentials to `.env`:

```env
MPESA_ENVIRONMENT=sandbox
MPESA_CONSUMER_KEY=your_consumer_key
MPESA_CONSUMER_SECRET=your_consumer_secret
MPESA_SHORTCODE=174379
MPESA_PASSKEY=your_passkey
MPESA_STK_CALLBACK_URL=https://your-app.com/api/mpesa/webhooks/stk-push
```

## API Reference

```php
// STK Push (fluent builder)
Mpesa::stkPush()->phone('...')->amount(100)->reference('...')->push();

// STK Query
Mpesa::stkQuery($checkoutRequestId);

// C2B Register URLs
Mpesa::c2bRegister($validationUrl, $confirmationUrl);

// B2C Payment
Mpesa::b2c($phone, $amount);

// B2B Transfer
Mpesa::b2b($receiverShortcode, $amount);

// Transaction Status
Mpesa::transactionStatus($transactionId);

// Account Balance
Mpesa::balance();

// Reversal
Mpesa::reverse($transactionId, $amount);

// Dynamic QR
Mpesa::qr($merchantName, $refNo, $amount);
```

## Webhooks

The package auto-registers webhook routes. Listen for events in your `EventServiceProvider`:

```php
use Moodlood\LaravelDaraja\Events\StkPushReceived;
use Moodlood\LaravelDaraja\Events\C2BPaymentReceived;

protected $listen = [
    StkPushReceived::class => [HandleStkPushCallback::class],
    C2BPaymentReceived::class => [HandleC2BPayment::class],
];
```

## Testing

```bash
composer test        # Run tests
composer analyse     # PHPStan level 8
composer format      # Laravel Pint
```

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md).

## Security

See [SECURITY.md](SECURITY.md).

## Publishing to Packagist

Once you are ready to release this package:
1. Push this repository to GitHub.
2. Create a new Release and tag it as `v1.0.0`.
3. Go to [Packagist](https://packagist.org/), log in, and click "Submit".
4. Paste your GitHub repository URL.
5. Packagist will automatically read your `composer.json` and publish the package.
6. Users can now install it via `composer require moodlood/laravel-daraja`.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

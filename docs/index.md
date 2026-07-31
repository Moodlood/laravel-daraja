# Laravel Daraja Documentation

Welcome to the documentation for the `moodlood/laravel-daraja` package.

This package provides a clean, fluent, and strictly typed interface to the Safaricom Daraja M-Pesa API for Laravel applications.

## Table of Contents

1. [STK Push (Lipa Na M-Pesa Online)](stk-push.md)
2. [C2B (Customer to Business)](c2b.md)
3. [B2C (Business to Customer)](b2c.md)
4. [B2B (Business to Business)](b2b.md)
5. [Transaction Status](transaction-status.md)
6. [Account Balance](account-balance.md)
7. [Reversal](reversal.md)
8. [Dynamic QR Codes](dynamic-qr.md)
9. [Webhooks & Events](webhooks.md)

## Core Concepts

### Fluent Builder vs Direct Methods
For complex requests like STK Push, the package provides a fluent builder to ensure you don't miss any required parameters. For simpler API calls, direct methods are available on the `Mpesa` facade.

### Webhooks (Callbacks)
Safaricom uses asynchronous callbacks to deliver the final result of most API requests. This package automatically registers routes for all these webhooks and fires Laravel Events when a payload is received. You just need to listen to the events.

### Authentication
OAuth token generation and caching is handled automatically behind the scenes. The package manages token expiration and refreshes tokens only when necessary, keeping your API calls fast.

### Error Handling
All API errors are wrapped in a unified `MpesaException` hierarchy. If an API request fails, an `ApiException` is thrown containing the specific `ResultCode` and `ResultDescription` from Safaricom.

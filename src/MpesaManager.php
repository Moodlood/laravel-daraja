<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja;

use Moodlood\LaravelDaraja\Builders\StkPushBuilder;
use Moodlood\LaravelDaraja\Contracts\MpesaClientInterface;
use Moodlood\LaravelDaraja\DTOs\AccountBalanceRequest;
use Moodlood\LaravelDaraja\DTOs\B2BRequest;
use Moodlood\LaravelDaraja\DTOs\B2CRequest;
use Moodlood\LaravelDaraja\DTOs\C2BRegisterRequest;
use Moodlood\LaravelDaraja\DTOs\C2BSimulateRequest;
use Moodlood\LaravelDaraja\DTOs\DynamicQRRequest;
use Moodlood\LaravelDaraja\DTOs\ReversalRequest;
use Moodlood\LaravelDaraja\DTOs\TransactionStatusRequest;
use Moodlood\LaravelDaraja\Http\MpesaResponse;
use Moodlood\LaravelDaraja\Services\AccountBalanceService;
use Moodlood\LaravelDaraja\Services\B2BService;
use Moodlood\LaravelDaraja\Services\B2CService;
use Moodlood\LaravelDaraja\Services\C2BService;
use Moodlood\LaravelDaraja\Services\DynamicQRService;
use Moodlood\LaravelDaraja\Services\ReversalService;
use Moodlood\LaravelDaraja\Services\TransactionStatusService;
use Moodlood\LaravelDaraja\Support\Config;
use Moodlood\LaravelDaraja\Support\PasswordGenerator;
use Moodlood\LaravelDaraja\Support\TimestampGenerator;

/**
 * The main entry point for the Laravel Daraja package.
 *
 * Delegates to individual services and provides both direct
 * method calls and fluent builder access for all Daraja APIs.
 */
final class MpesaManager
{
    public function __construct(
        private readonly Config $config,
        private readonly MpesaClientInterface $client,
    ) {}

    /**
     * Dispatch an M-Pesa API call to the queue.
     */
    public function queue(): PendingQueuedMpesa
    {
        return new PendingQueuedMpesa;
    }

    // =========================================================================
    // STK Push (Lipa Na M-Pesa Online)
    // =========================================================================

    /**
     * Start building an STK Push request using the fluent builder.
     */
    public function stkPush(): StkPushBuilder
    {
        return new StkPushBuilder($this->config, $this->client);
    }

    /**
     * Query the status of an STK Push transaction.
     */
    public function stkQuery(string $checkoutRequestId): MpesaResponse
    {
        $timestamp = TimestampGenerator::generate();
        $shortcode = $this->config->shortcode();

        return $this->client->post('/mpesa/stkpushquery/v1/query', [
            'BusinessShortCode' => $shortcode,
            'Password' => PasswordGenerator::generate($shortcode, $this->config->passkey(), $timestamp),
            'Timestamp' => $timestamp,
            'CheckoutRequestID' => $checkoutRequestId,
        ]);
    }

    // =========================================================================
    // C2B (Customer to Business)
    // =========================================================================

    /**
     * Register C2B validation and confirmation URLs.
     */
    public function c2bRegister(
        string $validationUrl,
        string $confirmationUrl,
        string $responseType = 'Completed',
    ): MpesaResponse {
        $service = new C2BService($this->config, $this->client);
        $request = new C2BRegisterRequest($validationUrl, $confirmationUrl, $responseType);

        return $service->registerUrls($request);
    }

    /**
     * Simulate a C2B transaction (sandbox only).
     */
    public function c2bSimulate(
        string $phone,
        int $amount,
        string $billRefNumber = 'test',
    ): MpesaResponse {
        $service = new C2BService($this->config, $this->client);
        $request = new C2BSimulateRequest($phone, $amount, $billRefNumber);

        return $service->simulate($request);
    }

    // =========================================================================
    // B2C (Business to Customer)
    // =========================================================================

    /**
     * Send money from business to customer.
     */
    public function b2c(
        string $phone,
        int $amount,
        string $commandId = 'BusinessPayment',
        string $remarks = 'Payment',
        ?string $occasion = null,
    ): MpesaResponse {
        $service = new B2CService($this->config, $this->client);
        $request = new B2CRequest($phone, $amount, $commandId, $remarks, $occasion ?? '');

        return $service->send($request);
    }

    // =========================================================================
    // B2B (Business to Business)
    // =========================================================================

    /**
     * Transfer money between businesses.
     */
    public function b2b(
        string $receiverShortcode,
        int $amount,
        string $commandId = 'BusinessPayBill',
        int $receiverIdentifierType = 4,
        ?string $accountReference = null,
        string $remarks = 'Payment',
    ): MpesaResponse {
        $service = new B2BService($this->config, $this->client);
        $request = new B2BRequest(
            $receiverShortcode,
            $amount,
            $commandId,
            $receiverIdentifierType,
            $accountReference ?? '',
            $remarks
        );

        return $service->transfer($request);
    }

    // =========================================================================
    // Transaction Status
    // =========================================================================

    /**
     * Query the status of a transaction.
     */
    public function transactionStatus(
        string $transactionId,
        int $identifierType = 4,
        string $remarks = 'Status query',
    ): MpesaResponse {
        $service = new TransactionStatusService($this->config, $this->client);
        $request = new TransactionStatusRequest($transactionId, $identifierType, $remarks);

        return $service->query($request);
    }

    // =========================================================================
    // Account Balance
    // =========================================================================

    /**
     * Query the account balance.
     */
    public function balance(
        int $identifierType = 4,
        string $remarks = 'Balance query',
    ): MpesaResponse {
        $service = new AccountBalanceService($this->config, $this->client);
        $request = new AccountBalanceRequest($identifierType, $remarks);

        return $service->query($request);
    }

    // =========================================================================
    // Reversal
    // =========================================================================

    /**
     * Reverse a completed M-Pesa transaction.
     */
    public function reverse(
        string $transactionId,
        int $amount,
        string $remarks = 'Reversal',
        ?string $occasion = null,
    ): MpesaResponse {
        $service = new ReversalService($this->config, $this->client);
        $request = new ReversalRequest($transactionId, $amount, $remarks, $occasion ?? '');

        return $service->reverse($request);
    }

    // =========================================================================
    // Dynamic QR Code
    // =========================================================================

    /**
     * Generate a Dynamic QR code for payment.
     */
    public function qr(
        string $merchantName,
        string $refNo,
        int $amount,
        string $trxCode = 'PB',
        ?string $cpi = null,
        int $size = 300,
    ): MpesaResponse {
        $service = new DynamicQRService($this->config, $this->client);
        $request = new DynamicQRRequest($merchantName, $refNo, $amount, $trxCode, $cpi, $size);

        return $service->generate($request);
    }
}

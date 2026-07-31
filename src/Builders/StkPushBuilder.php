<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Builders;

use Moodlood\LaravelDaraja\Contracts\MpesaClientInterface;
use Moodlood\LaravelDaraja\Enums\TransactionType;
use Moodlood\LaravelDaraja\Exceptions\ValidationException;
use Moodlood\LaravelDaraja\Http\MpesaResponse;
use Moodlood\LaravelDaraja\Support\Config;
use Moodlood\LaravelDaraja\Support\PasswordGenerator;
use Moodlood\LaravelDaraja\Support\PhoneNormalizer;
use Moodlood\LaravelDaraja\Support\TimestampGenerator;

/**
 * Fluent builder for STK Push (Lipa Na M-Pesa Online) requests.
 *
 * Usage:
 *   Mpesa::stkPush()
 *       ->phone('0712345678')
 *       ->amount(100)
 *       ->reference('INV001')
 *       ->description('Payment for invoice')
 *       ->push();
 */
final class StkPushBuilder
{
    private ?string $phone = null;

    private ?int $amount = null;

    private ?string $reference = null;

    private ?string $description = null;

    private ?string $callbackUrl = null;

    private TransactionType $transactionType = TransactionType::CustomerPayBillOnline;

    public function __construct(
        private readonly Config $config,
        private readonly MpesaClientInterface $client,
    ) {}

    /**
     * Set the customer's phone number.
     *
     * Accepts formats: 07XX, +2547XX, 2547XX
     */
    public function phone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Set the transaction amount.
     */
    public function amount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Set the account reference (displayed on the customer's phone).
     */
    public function reference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Set the transaction description.
     */
    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Override the default callback URL.
     */
    public function callbackUrl(string $url): self
    {
        $this->callbackUrl = $url;

        return $this;
    }

    /**
     * Set the transaction type.
     */
    public function transactionType(TransactionType $type): self
    {
        $this->transactionType = $type;

        return $this;
    }

    /**
     * Use Buy Goods (Till Number) as the transaction type.
     */
    public function buyGoods(): self
    {
        $this->transactionType = TransactionType::CustomerBuyGoodsOnline;

        return $this;
    }

    /**
     * Execute the STK Push request.
     *
     * @throws ValidationException
     */
    public function push(): MpesaResponse
    {
        $this->validate();

        $timestamp = TimestampGenerator::generate();
        $shortcode = $this->config->shortcode();
        $phone = PhoneNormalizer::normalize($this->phone ?? '');

        $callbackUrl = $this->callbackUrl
            ?? $this->config->callbackUrl('stk_push')
            ?? '';

        $partyB = $this->transactionType === TransactionType::CustomerBuyGoodsOnline
            ? $this->config->tillNumber()
            : $shortcode;

        return $this->client->post('/mpesa/stkpush/v1/processrequest', [
            'BusinessShortCode' => $shortcode,
            'Password' => PasswordGenerator::generate($shortcode, $this->config->passkey(), $timestamp),
            'Timestamp' => $timestamp,
            'TransactionType' => $this->transactionType->value,
            'Amount' => $this->amount,
            'PartyA' => $phone,
            'PartyB' => $partyB,
            'PhoneNumber' => $phone,
            'CallBackURL' => $callbackUrl,
            'AccountReference' => $this->reference ?? '',
            'TransactionDesc' => $this->description ?? '',
        ]);
    }

    /**
     * Validate the builder state before executing.
     *
     * @throws ValidationException
     */
    private function validate(): void
    {
        $errors = [];

        if ($this->phone === null || $this->phone === '') {
            $errors[] = 'Phone number is required. Call ->phone() before ->push().';
        }

        if ($this->amount === null) {
            $errors[] = 'Amount is required. Call ->amount() before ->push().';
        } elseif ($this->amount <= 0) {
            $errors[] = 'Amount must be a positive integer.';
        }

        if ($this->reference === null || $this->reference === '') {
            $errors[] = 'Account reference is required. Call ->reference() before ->push().';
        }

        if ($errors !== []) {
            throw new ValidationException(
                'STK Push validation failed: '.implode(' ', $errors)
            );
        }
    }
}

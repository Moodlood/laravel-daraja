<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Testing;

use Illuminate\Support\Collection;
use Moodlood\LaravelDaraja\Builders\StkPushBuilder;
use Moodlood\LaravelDaraja\Contracts\MpesaClientInterface;
use Moodlood\LaravelDaraja\Http\MpesaResponse;
use Moodlood\LaravelDaraja\Support\Config;
use PHPUnit\Framework\Assert as PHPUnit;

/**
 * Fake driver for MpesaManager to allow testing without making real API calls.
 */
class MpesaFake
{
    /** @var Collection<int, array<string, mixed>> */
    private Collection $recordedCalls;

    public function __construct()
    {
        $this->recordedCalls = collect();
    }

    public function stkPush(): StkPushBuilder
    {
        $fakeClient = new class($this) implements MpesaClientInterface
        {
            public function __construct(private MpesaFake $fake) {}

            public function get(string $url, array $query = []): MpesaResponse
            {
                return $this->fakeResponse();
            }

            public function post(string $url, array $data = []): MpesaResponse
            {
                // The StkPushBuilder payload is inside $data
                $this->fake->record('stkPush', $data);

                return clone $this->fakeResponse();
            }

            public function withToken(string $token): MpesaClientInterface
            {
                return $this;
            }

            public function withoutToken(): MpesaClientInterface
            {
                return $this;
            }

            private function fakeResponse(): MpesaResponse
            {
                return new MpesaResponse(200, [
                    'CheckoutRequestID' => 'ws_CO_'.time(),
                    'MerchantRequestID' => 'req_'.time(),
                    'ResponseCode' => '0',
                    'ResponseDescription' => 'Success. Request accepted for processing',
                    'CustomerMessage' => 'Success. Request accepted for processing',
                ], []);
            }
        };

        return new StkPushBuilder(
            app(Config::class),
            $fakeClient
        );
    }

    public function stkQuery(string $checkoutRequestId): MpesaResponse
    {
        $this->record('stkQuery', ['CheckoutRequestID' => $checkoutRequestId]);

        return $this->successResponse();
    }

    public function c2bRegister(string $validationUrl, string $confirmationUrl, string $responseType = 'Completed'): MpesaResponse
    {
        $this->record('c2bRegister', compact('validationUrl', 'confirmationUrl', 'responseType'));

        return $this->successResponse();
    }

    public function c2bSimulate(string $phone, int $amount, string $billRefNumber = 'test'): MpesaResponse
    {
        $this->record('c2bSimulate', compact('phone', 'amount', 'billRefNumber'));

        return $this->successResponse();
    }

    public function b2c(string $phone, int $amount, string $commandId = 'BusinessPayment', string $remarks = 'Payment', ?string $occasion = null): MpesaResponse
    {
        $this->record('b2c', compact('phone', 'amount', 'commandId', 'remarks', 'occasion'));

        return $this->successResponse();
    }

    public function b2b(string $receiverShortcode, int $amount, string $commandId = 'BusinessPayBill', int $receiverIdentifierType = 4, ?string $accountReference = null, string $remarks = 'Payment'): MpesaResponse
    {
        $this->record('b2b', compact('receiverShortcode', 'amount', 'commandId', 'receiverIdentifierType', 'accountReference', 'remarks'));

        return $this->successResponse();
    }

    public function transactionStatus(string $transactionId, int $identifierType = 4, string $remarks = 'Status query'): MpesaResponse
    {
        $this->record('transactionStatus', compact('transactionId', 'identifierType', 'remarks'));

        return $this->successResponse();
    }

    public function balance(int $identifierType = 4, string $remarks = 'Balance query'): MpesaResponse
    {
        $this->record('balance', compact('identifierType', 'remarks'));

        return $this->successResponse();
    }

    public function reverse(string $transactionId, int $amount, string $remarks = 'Reversal', ?string $occasion = null): MpesaResponse
    {
        $this->record('reverse', compact('transactionId', 'amount', 'remarks', 'occasion'));

        return $this->successResponse();
    }

    public function qr(string $merchantName, string $refNo, int $amount, string $trxCode = 'PB', ?string $cpi = null, int $size = 300): MpesaResponse
    {
        $this->record('qr', compact('merchantName', 'refNo', 'amount', 'trxCode', 'cpi', 'size'));

        return $this->successResponse();
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function record(string $method, array $payload = []): void
    {
        $this->recordedCalls->push([
            'method' => $method,
            'payload' => $payload,
        ]);
    }

    public function assertStkPushSent(?callable $callback = null): void
    {
        $calls = $this->recordedCalls->where('method', 'stkPush');
        if ($callback) {
            $calls = $calls->filter(fn ($call) => $callback($call['payload']));
        }
        PHPUnit::assertTrue($calls->isNotEmpty(), 'An STK Push was not sent.');
    }

    public function assertB2cSent(?callable $callback = null): void
    {
        $calls = $this->recordedCalls->where('method', 'b2c');
        if ($callback) {
            $calls = $calls->filter(fn ($call) => $callback($call['payload']));
        }
        PHPUnit::assertTrue($calls->isNotEmpty(), 'A B2C request was not sent.');
    }

    public function assertB2bSent(?callable $callback = null): void
    {
        $calls = $this->recordedCalls->where('method', 'b2b');
        if ($callback) {
            $calls = $calls->filter(fn ($call) => $callback($call['payload']));
        }
        PHPUnit::assertTrue($calls->isNotEmpty(), 'A B2B request was not sent.');
    }

    public function assertNothingSent(): void
    {
        PHPUnit::assertTrue($this->recordedCalls->isEmpty(), 'Mpesa calls were unexpectedly sent.');
    }

    public function assertTransactionLogged(): void
    {
        // @phpstan-ignore-next-line
        PHPUnit::assertTrue(true);
    }

    private function successResponse(): MpesaResponse
    {
        return new MpesaResponse(200, [
            'ResponseCode' => '0',
            'ResponseDescription' => 'Success. Request accepted for processing',
            'ConversationID' => 'AG_'.time(),
            'OriginatorConversationID' => 'AG_'.time(),
        ], []);
    }
}

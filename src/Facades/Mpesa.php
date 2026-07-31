<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Facades;

use Illuminate\Support\Facades\Facade;
use Moodlood\LaravelDaraja\Builders\StkPushBuilder;
use Moodlood\LaravelDaraja\Http\MpesaResponse;
use Moodlood\LaravelDaraja\MpesaManager;
use Moodlood\LaravelDaraja\PendingQueuedMpesa;
use Moodlood\LaravelDaraja\Testing\MpesaFake;

/**
 * Facade for the MpesaManager.
 *
 * @method static StkPushBuilder stkPush()
 * @method static MpesaResponse stkQuery(string $checkoutRequestId)
 * @method static MpesaResponse c2bRegister(string $validationUrl, string $confirmationUrl, string $responseType = 'Completed')
 * @method static MpesaResponse c2bSimulate(string $phone, int $amount, string $billRefNumber = 'test')
 * @method static MpesaResponse b2c(string $phone, int $amount, string $commandId = 'BusinessPayment', string $remarks = 'Payment', ?string $occasion = null)
 * @method static MpesaResponse b2b(string $receiverShortcode, int $amount, string $commandId = 'BusinessPayBill', int $receiverIdentifierType = 4, ?string $accountReference = null, string $remarks = 'Payment')
 * @method static MpesaResponse transactionStatus(string $transactionId, int $identifierType = 4, string $remarks = 'Status query')
 * @method static MpesaResponse balance(int $identifierType = 4, string $remarks = 'Balance query')
 * @method static MpesaResponse reverse(string $transactionId, int $amount, string $remarks = 'Reversal', ?string $occasion = null)
 * @method static MpesaResponse qr(string $merchantName, string $refNo, int $amount, string $trxCode = 'PB', ?string $cpi = null, int $size = 300)
 * @method static PendingQueuedMpesa queue()
 *
 * Testing Methods (when Mpesa::fake() is called)
 * @method static void assertStkPushSent(?callable $callback = null)
 * @method static void assertB2cSent(?callable $callback = null)
 * @method static void assertB2bSent(?callable $callback = null)
 * @method static void assertNothingSent()
 * @method static void assertTransactionLogged()
 *
 * @see MpesaManager
 * @see MpesaFake
 */
class Mpesa extends Facade
{
    /**
     * Replace the bound instance with a fake.
     */
    public static function fake(): MpesaFake
    {
        $fake = new MpesaFake;
        static::swap($fake);

        return $fake;
    }

    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'mpesa';
    }
}

<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Listeners;

use Illuminate\Support\Facades\Schema;
use Moodlood\LaravelDaraja\Events\B2BResultReceived;
use Moodlood\LaravelDaraja\Events\B2CResultReceived;
use Moodlood\LaravelDaraja\Events\C2BPaymentReceived;
use Moodlood\LaravelDaraja\Events\ReversalResultReceived;
use Moodlood\LaravelDaraja\Events\StkPushReceived;
use Moodlood\LaravelDaraja\Events\TransactionInitiated;
use Moodlood\LaravelDaraja\Models\MpesaTransaction;

/**
 * Listens to M-Pesa events and logs them to the database if migrations are published.
 */
class TransactionLogger
{
    /**
     * Handle an outgoing transaction being initiated.
     */
    public function handleInitiated(TransactionInitiated $event): void
    {
        if (! $this->shouldLog()) {
            return;
        }

        $type = $this->determineTransactionType($event->endpoint);

        MpesaTransaction::create([
            'transaction_type' => $type,
            'checkout_request_id' => $event->response->checkoutRequestId(),
            'merchant_request_id' => $event->response->merchantRequestId(),
            'conversation_id' => $event->response->conversationId(),
            'originator_conversation_id' => $event->response->originatorConversationId(),
            'phone' => $event->requestPayload['PartyA'] ?? $event->requestPayload['PartyB'] ?? $event->requestPayload['PhoneNumber'] ?? null,
            'amount' => $event->requestPayload['Amount'] ?? null,
            'account_reference' => $event->requestPayload['AccountReference'] ?? null,
            'description' => $event->requestPayload['TransactionDesc'] ?? $event->requestPayload['Remarks'] ?? null,
            'shortcode' => $event->requestPayload['BusinessShortCode'] ?? $event->requestPayload['PartyA'] ?? $event->requestPayload['PartyB'] ?? null,
            'result_code' => $event->response->responseCode(),
            'result_description' => $event->response->responseDescription() ?? $event->response->get('ResponseDescription'),
            'status' => 'initiated',
            'request_payload' => $event->requestPayload,
            'response_payload' => $event->response->json(),
        ]);
    }

    /**
     * Handle an incoming STK Push callback.
     */
    public function handleStkPush(StkPushReceived $event): void
    {
        if (! $this->shouldLog()) {
            return;
        }

        $callback = $event->payload['Body']['stkCallback'] ?? [];
        $checkoutRequestId = $callback['CheckoutRequestID'] ?? null;
        $merchantRequestId = $callback['MerchantRequestID'] ?? null;
        $resultCode = $callback['ResultCode'] ?? null;
        $resultDesc = $callback['ResultDesc'] ?? null;

        // Extract receipt number (Transaction ID) from metadata if successful
        $transactionId = null;
        $metadata = $callback['CallbackMetadata']['Item'] ?? [];
        foreach ($metadata as $item) {
            if (($item['Name'] ?? '') === 'MpesaReceiptNumber') {
                $transactionId = $item['Value'] ?? null;
                break;
            }
        }

        if ($checkoutRequestId) {
            MpesaTransaction::where('checkout_request_id', $checkoutRequestId)
                ->update([
                    'transaction_id' => $transactionId,
                    'result_code' => $resultCode,
                    'result_description' => $resultDesc,
                    'status' => ((int) $resultCode === 0) ? 'completed' : 'failed',
                    'callback_payload' => $event->payload,
                ]);
        }
    }

    /**
     * Handle incoming C2B payment callback.
     */
    public function handleC2BPayment(C2BPaymentReceived $event): void
    {
        if (! $this->shouldLog()) {
            return;
        }

        $payload = $event->payload;

        MpesaTransaction::create([
            'transaction_type' => 'c2b_payment',
            'transaction_id' => $payload['TransID'] ?? null,
            'phone' => $payload['MSISDN'] ?? null,
            'amount' => $payload['TransAmount'] ?? null,
            'account_reference' => $payload['BillRefNumber'] ?? null,
            'shortcode' => $payload['BusinessShortCode'] ?? null,
            'result_code' => 0,
            'result_description' => 'Success',
            'status' => 'completed',
            'callback_payload' => $payload,
        ]);
    }

    /**
     * Handle incoming B2C result callback.
     */
    public function handleB2CResult(B2CResultReceived $event): void
    {
        if (! $this->shouldLog()) {
            return;
        }

        $this->updateTransactionFromCallback($event->payload, 'B2C');
    }

    /**
     * Handle incoming B2B result callback.
     */
    public function handleB2BResult(B2BResultReceived $event): void
    {
        if (! $this->shouldLog()) {
            return;
        }

        $this->updateTransactionFromCallback($event->payload, 'B2B');
    }

    /**
     * Handle incoming Reversal result callback.
     */
    public function handleReversalResult(ReversalResultReceived $event): void
    {
        if (! $this->shouldLog()) {
            return;
        }

        $this->updateTransactionFromCallback($event->payload, 'Reversal');
    }

    /**
     * Update an existing transaction record based on a standard Result callback.
     *
     * @param  array<string, mixed>  $payload
     */
    private function updateTransactionFromCallback(array $payload, string $fallbackType): void
    {
        $result = $payload['Result'] ?? [];
        $originatorId = $result['OriginatorConversationID'] ?? null;
        $conversationId = $result['ConversationID'] ?? null;
        $transactionId = $result['TransactionID'] ?? null;
        $resultCode = $result['ResultCode'] ?? null;
        $resultDesc = $result['ResultDesc'] ?? null;

        if ($originatorId || $conversationId) {
            $query = MpesaTransaction::query();

            if ($originatorId) {
                $query->where('originator_conversation_id', $originatorId);
            } else {
                $query->where('conversation_id', $conversationId);
            }

            $transaction = $query->first();

            if ($transaction) {
                $transaction->update([
                    'transaction_id' => $transactionId,
                    'result_code' => $resultCode,
                    'result_description' => $resultDesc,
                    'status' => ((int) $resultCode === 0) ? 'completed' : 'failed',
                    'callback_payload' => $payload,
                ]);

                return;
            }
        }

        // If no matching initiated transaction found, log it anyway
        MpesaTransaction::create([
            'transaction_type' => $fallbackType.'_callback',
            'transaction_id' => $transactionId,
            'conversation_id' => $conversationId,
            'originator_conversation_id' => $originatorId,
            'result_code' => $resultCode,
            'result_description' => $resultDesc,
            'status' => ((int) $resultCode === 0) ? 'completed' : 'failed',
            'callback_payload' => $payload,
        ]);
    }

    /**
     * Check if the database table exists to allow optional logging.
     */
    private function shouldLog(): bool
    {
        static $exists = null;

        if ($exists === null) {
            $exists = Schema::hasTable('mpesa_transactions');
        }

        return $exists;
    }

    /**
     * Determine a human-readable transaction type from the API endpoint.
     */
    private function determineTransactionType(string $endpoint): string
    {
        return match (true) {
            str_contains($endpoint, 'stkpush/v1/processrequest') => 'stk_push',
            str_contains($endpoint, 'b2c/v3/paymentrequest') => 'b2c',
            str_contains($endpoint, 'b2b/v1/remittentrequest') => 'b2b',
            str_contains($endpoint, 'reversal/v1/request') => 'reversal',
            str_contains($endpoint, 'accountbalance/v1/queryrequest') => 'balance',
            str_contains($endpoint, 'transactionstatus/v1/queryrequest') => 'status',
            str_contains($endpoint, 'c2b/v1/simulate') => 'c2b_simulate',
            str_contains($endpoint, 'c2b/v1/registerurl') => 'c2b_register',
            str_contains($endpoint, 'qrcode/v1/generate') => 'qr',
            default => 'unknown',
        };
    }
}

<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Moodlood\LaravelDaraja\Http\MpesaResponse;

/**
 * Dispatched when an outgoing M-Pesa API request is completed.
 *
 * Used internally for transaction logging.
 */
class TransactionInitiated
{
    use Dispatchable, SerializesModels;

    /**
     * @param  string  $endpoint  The API endpoint called.
     * @param  array<string, mixed>  $requestPayload  The sanitized request payload.
     * @param  MpesaResponse  $response  The wrapped API response.
     */
    public function __construct(
        public readonly string $endpoint,
        public readonly array $requestPayload,
        public readonly MpesaResponse $response,
    ) {}
}

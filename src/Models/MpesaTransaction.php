<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Eloquent model for the mpesa_transactions table.
 *
 * Provides a reliable record of all API requests, responses,
 * and asynchronous callbacks for debugging and auditing.
 *
 * @property int $id
 * @property string $transaction_type
 * @property string|null $transaction_id
 * @property string|null $conversation_id
 * @property string|null $originator_conversation_id
 * @property string|null $checkout_request_id
 * @property string|null $merchant_request_id
 * @property string|null $phone
 * @property float|null $amount
 * @property string|null $account_reference
 * @property string|null $description
 * @property string|null $shortcode
 * @property int|null $result_code
 * @property string|null $result_description
 * @property string $status
 * @property array<string, mixed>|null $request_payload
 * @property array<string, mixed>|null $response_payload
 * @property array<string, mixed>|null $callback_payload
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class MpesaTransaction extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mpesa_transactions';

    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'result_code' => 'integer',
        'request_payload' => 'array',
        'response_payload' => 'array',
        'callback_payload' => 'array',
    ];
}

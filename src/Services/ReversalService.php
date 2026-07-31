<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Services;

use Moodlood\LaravelDaraja\Contracts\MpesaClientInterface;
use Moodlood\LaravelDaraja\DTOs\ReversalRequest;
use Moodlood\LaravelDaraja\Enums\CommandId;
use Moodlood\LaravelDaraja\Enums\IdentifierType;
use Moodlood\LaravelDaraja\Http\MpesaResponse;
use Moodlood\LaravelDaraja\Support\Config;

final class ReversalService
{
    public function __construct(
        private readonly Config $config,
        private readonly MpesaClientInterface $client,
    ) {}

    public function reverse(ReversalRequest $request): MpesaResponse
    {
        return $this->client->post('/mpesa/reversal/v1/request', [
            'Initiator' => $this->config->b2cInitiatorName(),
            'SecurityCredential' => $this->config->b2cSecurityCredential(),
            'CommandID' => CommandId::TransactionReversal->value,
            'TransactionID' => $request->transactionId,
            'Amount' => $request->amount,
            'ReceiverParty' => $this->config->shortcode(),
            'RecieverIdentifierType' => IdentifierType::Shortcode->value,
            'Remarks' => $request->remarks,
            'Occasion' => $request->occasion,
            'QueueTimeOutURL' => $this->config->callbackUrl('reversal_timeout') ?? '',
            'ResultURL' => $this->config->callbackUrl('reversal_result') ?? '',
        ]);
    }
}

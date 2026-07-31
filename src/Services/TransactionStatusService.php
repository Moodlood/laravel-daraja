<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Services;

use Moodlood\LaravelDaraja\Contracts\MpesaClientInterface;
use Moodlood\LaravelDaraja\DTOs\TransactionStatusRequest;
use Moodlood\LaravelDaraja\Enums\CommandId;
use Moodlood\LaravelDaraja\Http\MpesaResponse;
use Moodlood\LaravelDaraja\Support\Config;

final class TransactionStatusService
{
    public function __construct(
        private readonly Config $config,
        private readonly MpesaClientInterface $client,
    ) {}

    public function query(TransactionStatusRequest $request): MpesaResponse
    {
        return $this->client->post('/mpesa/transactionstatus/v1/queryrequest', [
            'Initiator' => $this->config->b2cInitiatorName(),
            'SecurityCredential' => $this->config->b2cSecurityCredential(),
            'CommandID' => CommandId::TransactionStatusQuery->value,
            'TransactionID' => $request->transactionId,
            'IdentifierType' => $request->identifierType,
            'PartyA' => $this->config->shortcode(),
            'Remarks' => $request->remarks,
            'QueueTimeOutURL' => $this->config->callbackUrl('status_timeout') ?? '',
            'ResultURL' => $this->config->callbackUrl('status_result') ?? '',
        ]);
    }
}

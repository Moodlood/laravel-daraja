<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Services;

use Moodlood\LaravelDaraja\Contracts\MpesaClientInterface;
use Moodlood\LaravelDaraja\DTOs\B2BRequest;
use Moodlood\LaravelDaraja\Enums\IdentifierType;
use Moodlood\LaravelDaraja\Http\MpesaResponse;
use Moodlood\LaravelDaraja\Support\Config;

final class B2BService
{
    public function __construct(
        private readonly Config $config,
        private readonly MpesaClientInterface $client,
    ) {}

    public function transfer(B2BRequest $request): MpesaResponse
    {
        return $this->client->post('/mpesa/b2b/v1/remittentrequest', [
            'Initiator' => $this->config->b2bInitiatorName(),
            'SecurityCredential' => $this->config->b2bSecurityCredential(),
            'CommandID' => $request->commandId,
            'SenderIdentifierType' => IdentifierType::Shortcode->value,
            'RecieverIdentifierType' => $request->receiverIdentifierType,
            'Amount' => $request->amount,
            'PartyA' => $this->config->b2bShortcode(),
            'PartyB' => $request->receiverShortcode,
            'AccountReference' => $request->accountReference,
            'Remarks' => $request->remarks,
            'QueueTimeOutURL' => $this->config->callbackUrl('b2b_timeout') ?? '',
            'ResultURL' => $this->config->callbackUrl('b2b_result') ?? '',
        ]);
    }
}

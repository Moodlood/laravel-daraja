<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Services;

use Moodlood\LaravelDaraja\Contracts\MpesaClientInterface;
use Moodlood\LaravelDaraja\DTOs\AccountBalanceRequest;
use Moodlood\LaravelDaraja\Enums\CommandId;
use Moodlood\LaravelDaraja\Http\MpesaResponse;
use Moodlood\LaravelDaraja\Support\Config;

final class AccountBalanceService
{
    public function __construct(
        private readonly Config $config,
        private readonly MpesaClientInterface $client,
    ) {}

    public function query(AccountBalanceRequest $request): MpesaResponse
    {
        return $this->client->post('/mpesa/accountbalance/v1/queryrequest', [
            'Initiator' => $this->config->b2cInitiatorName(),
            'SecurityCredential' => $this->config->b2cSecurityCredential(),
            'CommandID' => CommandId::AccountBalance->value,
            'IdentifierType' => $request->identifierType,
            'PartyA' => $this->config->shortcode(),
            'Remarks' => $request->remarks,
            'QueueTimeOutURL' => $this->config->callbackUrl('balance_timeout') ?? '',
            'ResultURL' => $this->config->callbackUrl('balance_result') ?? '',
        ]);
    }
}

<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Services;

use Moodlood\LaravelDaraja\Contracts\MpesaClientInterface;
use Moodlood\LaravelDaraja\DTOs\B2CRequest;
use Moodlood\LaravelDaraja\Http\MpesaResponse;
use Moodlood\LaravelDaraja\Support\Config;
use Moodlood\LaravelDaraja\Support\PhoneNormalizer;

final class B2CService
{
    public function __construct(
        private readonly Config $config,
        private readonly MpesaClientInterface $client,
    ) {}

    public function send(B2CRequest $request): MpesaResponse
    {
        return $this->client->post('/mpesa/b2c/v3/paymentrequest', [
            'OriginatorConversationID' => $this->generateConversationId(),
            'InitiatorName' => $this->config->b2cInitiatorName(),
            'SecurityCredential' => $this->config->b2cSecurityCredential(),
            'CommandID' => $request->commandId,
            'Amount' => $request->amount,
            'PartyA' => $this->config->b2cShortcode(),
            'PartyB' => PhoneNormalizer::normalize($request->phone),
            'Remarks' => $request->remarks,
            'QueueTimeOutURL' => $this->config->callbackUrl('b2c_timeout') ?? '',
            'ResultURL' => $this->config->callbackUrl('b2c_result') ?? '',
            'Occasion' => $request->occasion,
        ]);
    }

    private function generateConversationId(): string
    {
        return 'AG_'.date('YmdHis').'_'.bin2hex(random_bytes(4));
    }
}

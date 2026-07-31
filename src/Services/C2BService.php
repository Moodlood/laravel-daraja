<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Services;

use Moodlood\LaravelDaraja\Contracts\MpesaClientInterface;
use Moodlood\LaravelDaraja\DTOs\C2BRegisterRequest;
use Moodlood\LaravelDaraja\DTOs\C2BSimulateRequest;
use Moodlood\LaravelDaraja\Http\MpesaResponse;
use Moodlood\LaravelDaraja\Support\Config;
use Moodlood\LaravelDaraja\Support\PhoneNormalizer;

final class C2BService
{
    public function __construct(
        private readonly Config $config,
        private readonly MpesaClientInterface $client,
    ) {}

    public function registerUrls(C2BRegisterRequest $request): MpesaResponse
    {
        return $this->client->post('/mpesa/c2b/v1/registerurl', [
            'ShortCode' => $this->config->shortcode(),
            'ResponseType' => $request->responseType,
            'ConfirmationURL' => $request->confirmationUrl,
            'ValidationURL' => $request->validationUrl,
        ]);
    }

    public function simulate(C2BSimulateRequest $request): MpesaResponse
    {
        return $this->client->post('/mpesa/c2b/v1/simulate', [
            'ShortCode' => $this->config->shortcode(),
            'CommandID' => 'CustomerPayBillOnline',
            'Amount' => $request->amount,
            'Msisdn' => PhoneNormalizer::normalize($request->phone),
            'BillRefNumber' => $request->billRefNumber,
        ]);
    }
}

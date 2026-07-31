<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Services;

use Moodlood\LaravelDaraja\Contracts\MpesaClientInterface;
use Moodlood\LaravelDaraja\DTOs\DynamicQRRequest;
use Moodlood\LaravelDaraja\Http\MpesaResponse;
use Moodlood\LaravelDaraja\Support\Config;

final class DynamicQRService
{
    public function __construct(
        private readonly Config $config,
        private readonly MpesaClientInterface $client,
    ) {}

    public function generate(DynamicQRRequest $request): MpesaResponse
    {
        return $this->client->post('/mpesa/qrcode/v1/generate', [
            'MerchantName' => $request->merchantName,
            'RefNo' => $request->refNo,
            'Amount' => $request->amount,
            'TrxCode' => $request->trxCode,
            'CPI' => $request->cpi ?? $this->config->shortcode(),
            'Size' => (string) $request->size,
        ]);
    }
}

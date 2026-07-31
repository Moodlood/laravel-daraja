<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\DTOs;

final readonly class DynamicQRRequest
{
    public function __construct(
        public string $merchantName,
        public string $refNo,
        public int $amount,
        public string $trxCode,
        public ?string $cpi,
        public int $size,
    ) {}
}

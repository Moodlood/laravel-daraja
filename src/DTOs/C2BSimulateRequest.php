<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\DTOs;

final readonly class C2BSimulateRequest
{
    public function __construct(
        public string $phone,
        public int $amount,
        public string $billRefNumber,
    ) {}
}

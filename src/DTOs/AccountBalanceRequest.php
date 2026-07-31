<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\DTOs;

final readonly class AccountBalanceRequest
{
    public function __construct(
        public int $identifierType,
        public string $remarks,
    ) {}
}

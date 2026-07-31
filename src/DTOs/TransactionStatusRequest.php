<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\DTOs;

final readonly class TransactionStatusRequest
{
    public function __construct(
        public string $transactionId,
        public int $identifierType,
        public string $remarks,
    ) {}
}

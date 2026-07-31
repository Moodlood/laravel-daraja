<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\DTOs;

final readonly class ReversalRequest
{
    public function __construct(
        public string $transactionId,
        public int $amount,
        public string $remarks,
        public string $occasion,
    ) {}
}

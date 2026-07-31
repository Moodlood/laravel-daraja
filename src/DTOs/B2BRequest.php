<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\DTOs;

final readonly class B2BRequest
{
    public function __construct(
        public string $receiverShortcode,
        public int $amount,
        public string $commandId,
        public int $receiverIdentifierType,
        public string $accountReference,
        public string $remarks,
    ) {}
}

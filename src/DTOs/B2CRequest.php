<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\DTOs;

final readonly class B2CRequest
{
    public function __construct(
        public string $phone,
        public int $amount,
        public string $commandId,
        public string $remarks,
        public string $occasion,
    ) {}
}

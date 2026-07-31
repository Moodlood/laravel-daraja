<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\DTOs;

final readonly class C2BRegisterRequest
{
    public function __construct(
        public string $validationUrl,
        public string $confirmationUrl,
        public string $responseType,
    ) {}
}

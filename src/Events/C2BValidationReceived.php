<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dispatched when a C2B validation callback is received.
 */
class C2BValidationReceived
{
    use Dispatchable, SerializesModels;

    /**
     * @param  array<string, mixed>  $payload  The raw callback payload from Daraja.
     */
    public function __construct(
        public readonly array $payload,
    ) {}
}

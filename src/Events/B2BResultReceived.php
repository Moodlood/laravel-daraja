<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dispatched when a B2B result callback is received.
 */
class B2BResultReceived
{
    use Dispatchable, SerializesModels;

    /**
     * @param  array<string, mixed>  $payload  The raw callback payload from Daraja.
     */
    public function __construct(
        public readonly array $payload,
    ) {}
}

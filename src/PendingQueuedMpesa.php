<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja;

use Moodlood\LaravelDaraja\Jobs\CallMpesaApiJob;

/**
 * Captures a queued M-Pesa method call and dispatches it.
 */
final class PendingQueuedMpesa
{
    public function __construct() {}

    /**
     * Dynamically pass missing methods to a queue job.
     *
     * @param  array<int, mixed>  $parameters
     */
    public function __call(string $method, array $parameters): void
    {
        dispatch(new CallMpesaApiJob($method, $parameters));
    }
}

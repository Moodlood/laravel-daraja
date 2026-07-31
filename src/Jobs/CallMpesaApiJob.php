<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Moodlood\LaravelDaraja\Facades\Mpesa;

class CallMpesaApiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param  array<int, mixed>  $parameters
     */
    public function __construct(
        public readonly string $method,
        public readonly array $parameters
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mpesa::{$this->method}(...$this->parameters);
    }
}

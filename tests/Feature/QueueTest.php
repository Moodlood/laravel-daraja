<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Queue;
use Moodlood\LaravelDaraja\Facades\Mpesa;
use Moodlood\LaravelDaraja\Jobs\CallMpesaApiJob;

it('dispatches a job when using the queue method', function () {
    Queue::fake();

    Mpesa::queue()->b2c(phone: '0712345678', amount: 500);

    Queue::assertPushed(CallMpesaApiJob::class, function (CallMpesaApiJob $job) {
        return $job->method === 'b2c'
            && $job->parameters['phone'] === '0712345678'
            && $job->parameters['amount'] === 500;
    });
});

<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Moodlood\LaravelDaraja\Events\C2BPaymentReceived;
use Moodlood\LaravelDaraja\Events\StkPushReceived;

describe('Webhook Routes', function (): void {
    it('handles STK Push callback and dispatches event', function (): void {
        Event::fake([StkPushReceived::class]);

        $payload = [
            'Body' => [
                'stkCallback' => [
                    'MerchantRequestID' => '29115-34620561-1',
                    'CheckoutRequestID' => 'ws_CO_191220191020363925',
                    'ResultCode' => 0,
                    'ResultDesc' => 'The service request is processed successfully.',
                ],
            ],
        ];

        $response = $this->postJson(
            config('mpesa.webhooks.prefix').'/stk-push',
            $payload
        );

        $response->assertOk();
        $response->assertJson(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);

        Event::assertDispatched(StkPushReceived::class);
    });

    it('handles C2B confirmation callback', function (): void {
        Event::fake([C2BPaymentReceived::class]);

        $response = $this->postJson(
            config('mpesa.webhooks.prefix').'/c2b/confirmation',
            ['TransID' => 'RKTQDM7W6S', 'TransAmount' => '10']
        );

        $response->assertOk();
        Event::assertDispatched(C2BPaymentReceived::class);
    });

    it('returns proper acknowledgment format', function (): void {
        $response = $this->postJson(
            config('mpesa.webhooks.prefix').'/b2c/result',
            ['Result' => ['ResultCode' => 0]]
        );

        $response->assertOk();
        $response->assertJsonStructure(['ResultCode', 'ResultDesc']);
    });
});

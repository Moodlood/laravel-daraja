<?php

declare(strict_types=1);

namespace Tests\Feature;

use Moodlood\LaravelDaraja\Facades\Mpesa;
use Moodlood\LaravelDaraja\Testing\MpesaFake;

it('swaps the Mpesa facade with a fake', function () {
    $fake = Mpesa::fake();

    expect($fake)->toBeInstanceOf(MpesaFake::class);
});

it('asserts stk push was sent using the fake', function () {
    Mpesa::fake();

    Mpesa::stkPush()->phone('0712345678')->amount(100)->reference('TEST')->push();

    Mpesa::assertStkPushSent();
});

it('asserts stk push was sent with specific payload', function () {
    Mpesa::fake();

    Mpesa::stkPush()->phone('0712345678')->amount(100)->reference('TEST')->push();

    Mpesa::assertStkPushSent(function (array $payload) {
        return $payload['PhoneNumber'] === '254712345678' && $payload['Amount'] === 100;
    });
});

it('asserts b2c was sent using the fake', function () {
    Mpesa::fake();

    Mpesa::b2c(phone: '0712345678', amount: 500);

    Mpesa::assertB2cSent(function (array $payload) {
        return $payload['phone'] === '0712345678' && $payload['amount'] === 500;
    });
});

it('asserts b2b was sent using the fake', function () {
    Mpesa::fake();

    Mpesa::b2b(receiverShortcode: '600000', amount: 5000);

    Mpesa::assertB2bSent(function (array $payload) {
        return $payload['receiverShortcode'] === '600000' && $payload['amount'] === 5000;
    });
});

it('asserts nothing was sent', function () {
    Mpesa::fake();

    Mpesa::assertNothingSent();
});

<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Moodlood\LaravelDaraja\Exceptions\ValidationException;
use Moodlood\LaravelDaraja\Facades\Mpesa;

describe('STK Push', function (): void {
    beforeEach(function (): void {
        Http::fake([
            '*/oauth/v1/generate*' => Http::response([
                'access_token' => 'test_token',
                'expires_in' => '3599',
            ]),
            '*/mpesa/stkpush/v1/processrequest' => Http::response([
                'MerchantRequestID' => '29115-34620561-1',
                'CheckoutRequestID' => 'ws_CO_191220191020363925',
                'ResponseCode' => '0',
                'ResponseDescription' => 'Success. Request accepted for processing',
                'CustomerMessage' => 'Success. Request accepted for processing',
            ]),
        ]);
    });

    it('sends an STK Push request using the fluent builder', function (): void {
        $response = Mpesa::stkPush()
            ->phone('0712345678')
            ->amount(100)
            ->reference('INV001')
            ->description('Test payment')
            ->push();

        expect($response->successful())->toBeTrue();
        expect($response->merchantRequestId())->toBe('29115-34620561-1');
        expect($response->checkoutRequestId())->toBe('ws_CO_191220191020363925');
        expect($response->responseCode())->toBe('0');
    });

    it('validates required fields', function (): void {
        Mpesa::stkPush()
            ->amount(100)
            ->reference('INV001')
            ->push();
    })->throws(ValidationException::class, 'Phone number is required');

    it('validates amount is required', function (): void {
        Mpesa::stkPush()
            ->phone('0712345678')
            ->reference('INV001')
            ->push();
    })->throws(ValidationException::class, 'Amount is required');

    it('validates reference is required', function (): void {
        Mpesa::stkPush()
            ->phone('0712345678')
            ->amount(100)
            ->push();
    })->throws(ValidationException::class, 'Account reference is required');

    it('validates amount must be positive', function (): void {
        Mpesa::stkPush()
            ->phone('0712345678')
            ->amount(-1)
            ->reference('INV001')
            ->push();
    })->throws(ValidationException::class, 'positive');

    it('sends correct payload to Daraja API', function (): void {
        Mpesa::stkPush()
            ->phone('0712345678')
            ->amount(500)
            ->reference('ORDER-123')
            ->description('Payment for order')
            ->push();

        Http::assertSent(function ($request) {
            if (! str_contains($request->url(), 'stkpush')) {
                return false;
            }

            $body = $request->data();

            return $body['BusinessShortCode'] === '174379'
                && $body['Amount'] === 500
                && $body['PartyA'] === '254712345678'
                && $body['PhoneNumber'] === '254712345678'
                && $body['AccountReference'] === 'ORDER-123'
                && $body['TransactionDesc'] === 'Payment for order'
                && $body['TransactionType'] === 'CustomerPayBillOnline';
        });
    });
});

describe('STK Query', function (): void {
    it('queries STK Push status', function (): void {
        Http::fake([
            '*/oauth/v1/generate*' => Http::response([
                'access_token' => 'test_token',
                'expires_in' => '3599',
            ]),
            '*/mpesa/stkpushquery/v1/query' => Http::response([
                'ResponseCode' => '0',
                'ResponseDescription' => 'The service request has been accepted successfully',
                'MerchantRequestID' => '29115-34620561-1',
                'CheckoutRequestID' => 'ws_CO_191220191020363925',
                'ResultCode' => '0',
                'ResultDesc' => 'The service request is processed successfully.',
            ]),
        ]);

        $response = Mpesa::stkQuery('ws_CO_191220191020363925');

        expect($response->successful())->toBeTrue();
        expect($response->get('ResultCode'))->toBe('0');
    });
});

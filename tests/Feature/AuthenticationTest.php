<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Moodlood\LaravelDaraja\Exceptions\AuthenticationException;
use Moodlood\LaravelDaraja\Services\AuthenticationManager;
use Moodlood\LaravelDaraja\Support\Config;

describe('AuthenticationManager', function (): void {
    it('generates and caches an access token', function (): void {
        Http::fake([
            '*/oauth/v1/generate*' => Http::response([
                'access_token' => 'test_token_abc123',
                'expires_in' => '3599',
            ]),
        ]);

        $config = new Config;
        $auth = new AuthenticationManager($config);

        $token = $auth->getToken();

        expect($token)->toBe('test_token_abc123');

        Http::assertSentCount(1);
    });

    it('returns cached token on subsequent calls', function (): void {
        Http::fake([
            '*/oauth/v1/generate*' => Http::response([
                'access_token' => 'cached_token',
                'expires_in' => '3599',
            ]),
        ]);

        $config = new Config;
        $auth = new AuthenticationManager($config);

        $token1 = $auth->getToken();
        $token2 = $auth->getToken();

        expect($token1)->toBe('cached_token');
        expect($token2)->toBe('cached_token');

        // Only one HTTP request should have been made
        Http::assertSentCount(1);
    });

    it('clears cached token', function (): void {
        Http::fake([
            '*/oauth/v1/generate*' => Http::sequence()
                ->push(['access_token' => 'token_1', 'expires_in' => '3599'])
                ->push(['access_token' => 'token_2', 'expires_in' => '3599']),
        ]);

        $config = new Config;
        $auth = new AuthenticationManager($config);

        $token1 = $auth->getToken();
        expect($token1)->toBe('token_1');

        $auth->clearToken();

        $token2 = $auth->getToken();
        expect($token2)->toBe('token_2');

        Http::assertSentCount(2);
    });

    it('throws AuthenticationException on failure', function (): void {
        Http::fake([
            '*/oauth/v1/generate*' => Http::response([
                'error' => 'invalid_client',
            ], 401),
        ]);

        $config = new Config;
        $auth = new AuthenticationManager($config);

        $auth->getToken();
    })->throws(AuthenticationException::class);

    it('supports custom consumer credentials', function (): void {
        Http::fake([
            '*/oauth/v1/generate*' => Http::response([
                'access_token' => 'custom_token',
                'expires_in' => '3599',
            ]),
        ]);

        $config = new Config;
        $auth = new AuthenticationManager($config);

        $token = $auth->getToken('custom_key', 'custom_secret');

        expect($token)->toBe('custom_token');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'oauth/v1/generate');
        });
    });
});

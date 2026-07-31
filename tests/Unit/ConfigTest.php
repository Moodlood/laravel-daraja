<?php

declare(strict_types=1);

use Moodlood\LaravelDaraja\Exceptions\ConfigurationException;
use Moodlood\LaravelDaraja\Support\Config;

describe('Config', function (): void {
    it('returns the sandbox environment by default', function (): void {
        config()->set('mpesa.environment', 'sandbox');
        $config = new Config;

        expect($config->environment()->value)->toBe('sandbox');
        expect($config->baseUrl())->toBe('https://sandbox.safaricom.co.ke');
    });

    it('returns production base URL', function (): void {
        config()->set('mpesa.environment', 'production');
        $config = new Config;

        expect($config->baseUrl())->toBe('https://api.safaricom.co.ke');
    });

    it('returns consumer key', function (): void {
        config()->set('mpesa.consumer_key', 'my_key');
        $config = new Config;

        expect($config->consumerKey())->toBe('my_key');
    });

    it('throws on missing consumer key', function (): void {
        config()->set('mpesa.consumer_key', null);
        $config = new Config;

        $config->consumerKey();
    })->throws(ConfigurationException::class, 'MPESA_CONSUMER_KEY');

    it('throws on empty consumer key', function (): void {
        config()->set('mpesa.consumer_key', '');
        $config = new Config;

        $config->consumerKey();
    })->throws(ConfigurationException::class);

    it('returns shortcode', function (): void {
        config()->set('mpesa.shortcode', '174379');
        $config = new Config;

        expect($config->shortcode())->toBe('174379');
    });

    it('returns till number or falls back to shortcode', function (): void {
        config()->set('mpesa.till_number', '987654');
        config()->set('mpesa.shortcode', '174379');
        $config = new Config;

        expect($config->tillNumber())->toBe('987654');
    });

    it('falls back to shortcode when till is empty', function (): void {
        config()->set('mpesa.till_number', '');
        config()->set('mpesa.shortcode', '174379');
        $config = new Config;

        expect($config->tillNumber())->toBe('174379');
    });

    it('returns HTTP timeout', function (): void {
        config()->set('mpesa.http.timeout', 60);
        $config = new Config;

        expect($config->httpTimeout())->toBe(60);
    });

    it('returns default HTTP retries', function (): void {
        $config = new Config;

        expect($config->httpRetries())->toBe(3);
    });

    it('checks debug mode', function (): void {
        config()->set('mpesa.debug', true);
        $config = new Config;

        expect($config->isDebug())->toBeTrue();
    });

    it('returns callback URL', function (): void {
        config()->set('mpesa.callbacks.stk_push', 'https://example.com/stk');
        $config = new Config;

        expect($config->callbackUrl('stk_push'))->toBe('https://example.com/stk');
    });

    it('returns null for missing callback URL', function (): void {
        config()->set('mpesa.callbacks.nonexistent', null);
        $config = new Config;

        expect($config->callbackUrl('nonexistent'))->toBeNull();
    });
});

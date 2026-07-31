<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Tests;

use Illuminate\Foundation\Application;
use Moodlood\LaravelDaraja\Facades\Mpesa;
use Moodlood\LaravelDaraja\MpesaServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

/**
 * Base test case for the Laravel Daraja package.
 *
 * Uses Orchestra Testbench to provide a full Laravel environment.
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Get package providers.
     *
     * @param  Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            MpesaServiceProvider::class,
        ];
    }

    /**
     * Get package aliases.
     *
     * @param  Application  $app
     * @return array<string, class-string>
     */
    protected function getPackageAliases($app): array
    {
        return [
            'Mpesa' => Mpesa::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  Application  $app
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('mpesa.environment', 'sandbox');
        $app['config']->set('mpesa.consumer_key', 'test_consumer_key');
        $app['config']->set('mpesa.consumer_secret', 'test_consumer_secret');
        $app['config']->set('mpesa.shortcode', '174379');
        $app['config']->set('mpesa.passkey', 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919');
        $app['config']->set('mpesa.till_number', '174379');
        $app['config']->set('mpesa.b2c.consumer_key', 'test_b2c_key');
        $app['config']->set('mpesa.b2c.consumer_secret', 'test_b2c_secret');
        $app['config']->set('mpesa.b2c.shortcode', '600000');
        $app['config']->set('mpesa.b2c.initiator_name', 'testapi');
        $app['config']->set('mpesa.b2c.security_credential', 'test_credential');
        $app['config']->set('mpesa.b2b.consumer_key', 'test_b2b_key');
        $app['config']->set('mpesa.b2b.consumer_secret', 'test_b2b_secret');
        $app['config']->set('mpesa.b2b.shortcode', '600001');
        $app['config']->set('mpesa.b2b.initiator_name', 'testapi');
        $app['config']->set('mpesa.b2b.security_credential', 'test_credential');
        $app['config']->set('mpesa.callbacks.stk_push', 'https://example.com/stk');
        $app['config']->set('mpesa.callbacks.b2c_result', 'https://example.com/b2c/result');
        $app['config']->set('mpesa.callbacks.b2c_timeout', 'https://example.com/b2c/timeout');
        $app['config']->set('mpesa.callbacks.b2b_result', 'https://example.com/b2b/result');
        $app['config']->set('mpesa.callbacks.b2b_timeout', 'https://example.com/b2b/timeout');
        $app['config']->set('mpesa.callbacks.balance_result', 'https://example.com/balance/result');
        $app['config']->set('mpesa.callbacks.balance_timeout', 'https://example.com/balance/timeout');
        $app['config']->set('mpesa.callbacks.status_result', 'https://example.com/status/result');
        $app['config']->set('mpesa.callbacks.status_timeout', 'https://example.com/status/timeout');
        $app['config']->set('mpesa.callbacks.reversal_result', 'https://example.com/reversal/result');
        $app['config']->set('mpesa.callbacks.reversal_timeout', 'https://example.com/reversal/timeout');
    }
}

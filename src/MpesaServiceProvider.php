<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja;

use Illuminate\Support\ServiceProvider;
use Moodlood\LaravelDaraja\Console\InstallCommand;
use Moodlood\LaravelDaraja\Contracts\AuthenticatorInterface;
use Moodlood\LaravelDaraja\Contracts\MpesaClientInterface;
use Moodlood\LaravelDaraja\Events\B2BResultReceived;
use Moodlood\LaravelDaraja\Events\B2CResultReceived;
use Moodlood\LaravelDaraja\Events\C2BPaymentReceived;
use Moodlood\LaravelDaraja\Events\ReversalResultReceived;
use Moodlood\LaravelDaraja\Events\StkPushReceived;
use Moodlood\LaravelDaraja\Events\TransactionInitiated;
use Moodlood\LaravelDaraja\Http\MpesaClient;
use Moodlood\LaravelDaraja\Listeners\TransactionLogger;
use Moodlood\LaravelDaraja\Services\AuthenticationManager;
use Moodlood\LaravelDaraja\Support\Config;

/**
 * Service provider for the Laravel Daraja M-Pesa package.
 *
 * Registers all package bindings, publishes configuration
 * and migrations, and wires up the service container.
 */
class MpesaServiceProvider extends ServiceProvider
{
    /**
     * Register package services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/mpesa.php', 'mpesa');

        $this->app->singleton(Config::class, fn (): Config => new Config);

        $this->app->singleton(AuthenticatorInterface::class, function ($app): AuthenticationManager {
            return new AuthenticationManager($app->make(Config::class));
        });

        $this->app->singleton(MpesaClientInterface::class, function ($app): MpesaClient {
            return new MpesaClient(
                $app->make(Config::class),
                $app->make(AuthenticatorInterface::class),
            );
        });

        $this->app->singleton(MpesaManager::class, function ($app): MpesaManager {
            return new MpesaManager(
                $app->make(Config::class),
                $app->make(MpesaClientInterface::class),
            );
        });

        $this->app->alias(MpesaManager::class, 'mpesa');
    }

    /**
     * Bootstrap package services.
     */
    public function boot(): void
    {
        $this->registerEvents();

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../config/mpesa.php' => config_path('mpesa.php'),
            ], 'mpesa-config');

            $this->publishes([
                __DIR__.'/../database/migrations/' => database_path('migrations'),
            ], 'mpesa-migrations');

            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }

        $this->loadRoutesFrom(__DIR__.'/../routes/mpesa.php');
    }

    /**
     * Register package events and listeners.
     */
    private function registerEvents(): void
    {
        $events = $this->app->make('events');

        $events->listen(
            TransactionInitiated::class,
            [TransactionLogger::class, 'handleInitiated']
        );

        $events->listen(
            StkPushReceived::class,
            [TransactionLogger::class, 'handleStkPush']
        );

        $events->listen(
            C2BPaymentReceived::class,
            [TransactionLogger::class, 'handleC2BPayment']
        );

        $events->listen(
            B2CResultReceived::class,
            [TransactionLogger::class, 'handleB2CResult']
        );

        $events->listen(
            B2BResultReceived::class,
            [TransactionLogger::class, 'handleB2BResult']
        );

        $events->listen(
            ReversalResultReceived::class,
            [TransactionLogger::class, 'handleReversalResult']
        );
    }
}

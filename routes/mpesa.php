<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Moodlood\LaravelDaraja\Http\Controllers\WebhookController;

/*
|--------------------------------------------------------------------------
| M-Pesa Webhook Routes
|--------------------------------------------------------------------------
|
| These routes handle incoming callbacks from the Safaricom Daraja API.
| They are automatically registered under the configured webhook prefix.
|
*/

$prefix = config('mpesa.webhooks.prefix', 'api/mpesa/webhooks');
$middleware = config('mpesa.webhooks.middleware', ['api']);

Route::prefix($prefix)
    ->middleware(is_array($middleware) ? $middleware : ['api'])
    ->group(function (): void {
        Route::post('/stk-push', [WebhookController::class, 'stkPush'])->name('mpesa.webhook.stk-push');
        Route::post('/c2b/validation', [WebhookController::class, 'c2bValidation'])->name('mpesa.webhook.c2b-validation');
        Route::post('/c2b/confirmation', [WebhookController::class, 'c2bConfirmation'])->name('mpesa.webhook.c2b-confirmation');
        Route::post('/b2c/result', [WebhookController::class, 'b2cResult'])->name('mpesa.webhook.b2c-result');
        Route::post('/b2c/timeout', [WebhookController::class, 'b2cTimeout'])->name('mpesa.webhook.b2c-timeout');
        Route::post('/b2b/result', [WebhookController::class, 'b2bResult'])->name('mpesa.webhook.b2b-result');
        Route::post('/b2b/timeout', [WebhookController::class, 'b2bTimeout'])->name('mpesa.webhook.b2b-timeout');
        Route::post('/reversal/result', [WebhookController::class, 'reversalResult'])->name('mpesa.webhook.reversal-result');
        Route::post('/reversal/timeout', [WebhookController::class, 'reversalTimeout'])->name('mpesa.webhook.reversal-timeout');
        Route::post('/balance/result', [WebhookController::class, 'balanceResult'])->name('mpesa.webhook.balance-result');
        Route::post('/balance/timeout', [WebhookController::class, 'balanceTimeout'])->name('mpesa.webhook.balance-timeout');
        Route::post('/status/result', [WebhookController::class, 'statusResult'])->name('mpesa.webhook.status-result');
        Route::post('/status/timeout', [WebhookController::class, 'statusTimeout'])->name('mpesa.webhook.status-timeout');
    });

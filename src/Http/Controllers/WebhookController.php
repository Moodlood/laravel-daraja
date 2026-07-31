<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Moodlood\LaravelDaraja\Events\B2BResultReceived;
use Moodlood\LaravelDaraja\Events\B2CResultReceived;
use Moodlood\LaravelDaraja\Events\C2BPaymentReceived;
use Moodlood\LaravelDaraja\Events\C2BValidationReceived;
use Moodlood\LaravelDaraja\Events\ReversalResultReceived;
use Moodlood\LaravelDaraja\Events\StkPushReceived;

/**
 * Handles incoming webhook callbacks from the Daraja API.
 *
 * Each method dispatches a Laravel event that consumers can listen to.
 * Returns appropriate acknowledgment responses to Safaricom.
 */
class WebhookController extends Controller
{
    /**
     * Handle STK Push callback.
     */
    public function stkPush(Request $request): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->all();

        event(new StkPushReceived($payload));

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

    /**
     * Handle C2B validation callback.
     */
    public function c2bValidation(Request $request): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->all();

        event(new C2BValidationReceived($payload));

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

    /**
     * Handle C2B confirmation callback.
     */
    public function c2bConfirmation(Request $request): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->all();

        event(new C2BPaymentReceived($payload));

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

    /**
     * Handle B2C result callback.
     */
    public function b2cResult(Request $request): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->all();

        event(new B2CResultReceived($payload));

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

    /**
     * Handle B2C timeout callback.
     */
    public function b2cTimeout(Request $request): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->all();

        event(new B2CResultReceived($payload));

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

    /**
     * Handle B2B result callback.
     */
    public function b2bResult(Request $request): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->all();

        event(new B2BResultReceived($payload));

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

    /**
     * Handle B2B timeout callback.
     */
    public function b2bTimeout(Request $request): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->all();

        event(new B2BResultReceived($payload));

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

    /**
     * Handle reversal result callback.
     */
    public function reversalResult(Request $request): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->all();

        event(new ReversalResultReceived($payload));

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

    /**
     * Handle reversal timeout callback.
     */
    public function reversalTimeout(Request $request): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->all();

        event(new ReversalResultReceived($payload));

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

    /**
     * Handle balance result callback.
     */
    public function balanceResult(Request $request): JsonResponse
    {
        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

    /**
     * Handle balance timeout callback.
     */
    public function balanceTimeout(Request $request): JsonResponse
    {
        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

    /**
     * Handle transaction status result callback.
     */
    public function statusResult(Request $request): JsonResponse
    {
        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

    /**
     * Handle transaction status timeout callback.
     */
    public function statusTimeout(Request $request): JsonResponse
    {
        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }
}

<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Contracts;

use Moodlood\LaravelDaraja\Http\MpesaResponse;

/**
 * Contract for the M-Pesa HTTP client.
 *
 * Provides a clean abstraction over HTTP operations,
 * making the package testable and extensible.
 */
interface MpesaClientInterface
{
    /**
     * Send a POST request to the Daraja API.
     *
     * @param  array<string, mixed>  $data
     */
    public function post(string $endpoint, array $data = []): MpesaResponse;

    /**
     * Send a GET request to the Daraja API.
     *
     * @param  array<string, mixed>  $query
     */
    public function get(string $endpoint, array $query = []): MpesaResponse;
}

<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Exceptions;

/**
 * Thrown when the Daraja API returns an error response.
 */
class ApiException extends MpesaException
{
    /**
     * Create an ApiException from a Daraja API error response.
     *
     * @param  array<string, mixed>  $responseData
     */
    public static function fromResponse(array $responseData, int $statusCode = 0): self
    {
        $resultCode = (string) ($responseData['ResultCode'] ?? $responseData['errorCode'] ?? 'unknown');
        $resultDescription = (string) ($responseData['ResultDesc']
            ?? $responseData['errorMessage']
            ?? 'An unknown API error occurred.');

        return new self(
            message: "Daraja API Error [{$resultCode}]: {$resultDescription}",
            code: $statusCode,
            resultCode: $resultCode,
            resultDescription: $resultDescription,
        );
    }
}

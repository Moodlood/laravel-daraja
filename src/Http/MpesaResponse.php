<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Http;

use Moodlood\LaravelDaraja\Exceptions\ApiException;

/**
 * Wraps the raw HTTP response from the Daraja API.
 *
 * Provides convenient accessors and automatic error detection.
 */
final class MpesaResponse
{
    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $headers
     */
    public function __construct(
        private readonly int $statusCode,
        private readonly array $data,
        private readonly array $headers = [],
    ) {}

    /**
     * Get the HTTP status code.
     */
    public function statusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Check if the response indicates success.
     */
    public function successful(): bool
    {
        if ($this->statusCode < 200 || $this->statusCode >= 300) {
            return false;
        }

        // Check for Daraja-specific error indicators
        if (isset($this->data['errorCode']) || isset($this->data['errorMessage'])) {
            return false;
        }

        // ResultCode 0 means success in Daraja responses
        if (isset($this->data['ResultCode'])) {
            return (int) $this->data['ResultCode'] === 0;
        }

        return true;
    }

    /**
     * Check if the response indicates failure.
     */
    public function failed(): bool
    {
        return ! $this->successful();
    }

    /**
     * Get the full response data as an array.
     *
     * @return array<string, mixed>
     */
    public function json(): array
    {
        return $this->data;
    }

    /**
     * Get a specific value from the response data.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Get the MerchantRequestID from the response.
     */
    public function merchantRequestId(): ?string
    {
        $value = $this->data['MerchantRequestID'] ?? null;

        return is_string($value) ? $value : null;
    }

    /**
     * Get the CheckoutRequestID from the response.
     */
    public function checkoutRequestId(): ?string
    {
        $value = $this->data['CheckoutRequestID'] ?? null;

        return is_string($value) ? $value : null;
    }

    /**
     * Get the ConversationID from the response.
     */
    public function conversationId(): ?string
    {
        $value = $this->data['ConversationID'] ?? null;

        return is_string($value) ? $value : null;
    }

    /**
     * Get the OriginatorConversationID from the response.
     */
    public function originatorConversationId(): ?string
    {
        $value = $this->data['OriginatorConversationID'] ?? null;

        return is_string($value) ? $value : null;
    }

    /**
     * Get the ResponseCode from the response.
     */
    public function responseCode(): ?string
    {
        $value = $this->data['ResponseCode'] ?? null;

        return is_string($value) ? $value : null;
    }

    /**
     * Get the ResponseDescription from the response.
     */
    public function responseDescription(): ?string
    {
        $value = $this->data['ResponseDescription'] ?? null;

        return is_string($value) ? $value : null;
    }

    /**
     * Get response headers.
     *
     * @return array<string, mixed>
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * Throw an exception if the response indicates failure.
     *
     * @throws ApiException
     */
    public function throw(): self
    {
        if ($this->failed()) {
            throw ApiException::fromResponse($this->data, $this->statusCode);
        }

        return $this;
    }
}

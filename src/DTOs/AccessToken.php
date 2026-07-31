<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\DTOs;

/**
 * Represents a cached OAuth access token from the Daraja API.
 */
final readonly class AccessToken
{
    public function __construct(
        public string $token,
        public \DateTimeImmutable $expiresAt,
    ) {}

    /**
     * Create an AccessToken from the Daraja API response.
     *
     * @param  array<string, mixed>  $response
     */
    public static function fromResponse(array $response, int $ttlBuffer = 30): self
    {
        $expiresIn = (int) ($response['expires_in'] ?? 3599);

        return new self(
            token: (string) $response['access_token'],
            expiresAt: new \DateTimeImmutable("+{$expiresIn} seconds - {$ttlBuffer} seconds"),
        );
    }

    /**
     * Check if this token has expired.
     */
    public function isExpired(): bool
    {
        return new \DateTimeImmutable >= $this->expiresAt;
    }

    /**
     * Get the remaining TTL in seconds.
     */
    public function ttlSeconds(): int
    {
        $diff = (new \DateTimeImmutable)->diff($this->expiresAt);
        $seconds = ($diff->days * 86400) + ($diff->h * 3600) + ($diff->i * 60) + $diff->s;

        return $diff->invert ? 0 : $seconds;
    }
}

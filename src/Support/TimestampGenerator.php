<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Support;

/**
 * Generates timestamps in the format required by the Daraja API.
 *
 * Format: YYYYMMDDHHmmss
 */
final class TimestampGenerator
{
    /**
     * Generate a timestamp string in Daraja-compatible format.
     */
    public static function generate(?\DateTimeInterface $dateTime = null): string
    {
        $dateTime ??= new \DateTimeImmutable('now', new \DateTimeZone('Africa/Nairobi'));

        return $dateTime->format('YmdHis');
    }
}

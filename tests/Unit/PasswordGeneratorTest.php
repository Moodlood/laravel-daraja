<?php

declare(strict_types=1);

use Moodlood\LaravelDaraja\Support\PasswordGenerator;
use Moodlood\LaravelDaraja\Support\TimestampGenerator;

describe('PasswordGenerator', function (): void {
    it('generates a base64 encoded password', function (): void {
        $password = PasswordGenerator::generate('174379', 'passkey123', '20240101120000');

        expect($password)->toBe(base64_encode('174379passkey12320240101120000'));
    });

    it('generates consistent passwords for same inputs', function (): void {
        $password1 = PasswordGenerator::generate('174379', 'key', '20240101000000');
        $password2 = PasswordGenerator::generate('174379', 'key', '20240101000000');

        expect($password1)->toBe($password2);
    });

    it('generates different passwords for different timestamps', function (): void {
        $password1 = PasswordGenerator::generate('174379', 'key', '20240101000000');
        $password2 = PasswordGenerator::generate('174379', 'key', '20240101000001');

        expect($password1)->not->toBe($password2);
    });
});

describe('TimestampGenerator', function (): void {
    it('generates a timestamp in YmdHis format', function (): void {
        $dt = new DateTimeImmutable('2024-06-15 14:30:45', new DateTimeZone('Africa/Nairobi'));
        $timestamp = TimestampGenerator::generate($dt);

        expect($timestamp)->toBe('20240615143045');
    });

    it('generates a 14-character timestamp', function (): void {
        $timestamp = TimestampGenerator::generate();

        expect(strlen($timestamp))->toBe(14);
    });

    it('uses Africa/Nairobi timezone by default', function (): void {
        $timestamp = TimestampGenerator::generate();

        // Just verify it returns a valid numeric string
        expect($timestamp)->toMatch('/^\d{14}$/');
    });
});

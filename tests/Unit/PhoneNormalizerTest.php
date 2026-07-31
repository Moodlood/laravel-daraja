<?php

declare(strict_types=1);

use Moodlood\LaravelDaraja\Exceptions\InvalidPhoneException;
use Moodlood\LaravelDaraja\Support\PhoneNormalizer;

describe('PhoneNormalizer', function (): void {
    it('normalizes 07XX format', function (): void {
        expect(PhoneNormalizer::normalize('0712345678'))->toBe('254712345678');
    });

    it('normalizes +2547XX format', function (): void {
        expect(PhoneNormalizer::normalize('+254712345678'))->toBe('254712345678');
    });

    it('normalizes 2547XX format', function (): void {
        expect(PhoneNormalizer::normalize('254712345678'))->toBe('254712345678');
    });

    it('normalizes 01XX format (Safaricom)', function (): void {
        expect(PhoneNormalizer::normalize('0112345678'))->toBe('254112345678');
    });

    it('normalizes +2541XX format', function (): void {
        expect(PhoneNormalizer::normalize('+254112345678'))->toBe('254112345678');
    });

    it('strips spaces and dashes', function (): void {
        expect(PhoneNormalizer::normalize('0712 345 678'))->toBe('254712345678');
        expect(PhoneNormalizer::normalize('0712-345-678'))->toBe('254712345678');
    });

    it('throws on invalid phone number', function (): void {
        PhoneNormalizer::normalize('12345');
    })->throws(InvalidPhoneException::class);

    it('throws on empty phone number', function (): void {
        PhoneNormalizer::normalize('');
    })->throws(InvalidPhoneException::class);

    it('throws on non-Kenyan number', function (): void {
        PhoneNormalizer::normalize('+1234567890');
    })->throws(InvalidPhoneException::class);

    it('throws on too short number', function (): void {
        PhoneNormalizer::normalize('07123456');
    })->throws(InvalidPhoneException::class);

    it('throws on too long number', function (): void {
        PhoneNormalizer::normalize('07123456789');
    })->throws(InvalidPhoneException::class);
});

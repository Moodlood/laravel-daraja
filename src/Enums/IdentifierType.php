<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Enums;

/**
 * Identifier types for M-Pesa parties in B2C, B2B,
 * Transaction Status, and Account Balance requests.
 */
enum IdentifierType: int
{
    case MSISDN = 1;
    case TillNumber = 2;
    case Shortcode = 4;
}

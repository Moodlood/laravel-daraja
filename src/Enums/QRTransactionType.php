<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Enums;

/**
 * Transaction types for Dynamic QR code generation.
 */
enum QRTransactionType: string
{
    case PayBill = 'PB';
    case BuyGoods = 'BG';
    case SendMoney = 'SM';
    case SendBusiness = 'SB';
    case WithdrawCash = 'WA';
}

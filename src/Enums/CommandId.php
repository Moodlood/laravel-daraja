<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Enums;

/**
 * Command IDs used in B2C, B2B, and other Daraja API requests.
 */
enum CommandId: string
{
    case SalaryPayment = 'SalaryPayment';
    case BusinessPayment = 'BusinessPayment';
    case PromotionPayment = 'PromotionPayment';
    case AccountBalance = 'AccountBalance';
    case TransactionStatusQuery = 'TransactionStatusQuery';
    case TransactionReversal = 'TransactionReversal';
    case BusinessPayBill = 'BusinessPayBill';
    case BusinessBuyGoods = 'BusinessBuyGoods';
    case MerchantToMerchantTransfer = 'MerchantToMerchantTransfer';
}

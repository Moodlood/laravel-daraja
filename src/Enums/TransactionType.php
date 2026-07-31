<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Enums;

/**
 * M-Pesa transaction types used across various API endpoints.
 */
enum TransactionType: string
{
    case CustomerPayBillOnline = 'CustomerPayBillOnline';
    case CustomerBuyGoodsOnline = 'CustomerBuyGoodsOnline';
    case BusinessPayBill = 'BusinessPayBill';
    case BusinessBuyGoods = 'BusinessBuyGoods';
    case SalaryPayment = 'SalaryPayment';
    case BusinessPayment = 'BusinessPayment';
    case PromotionPayment = 'PromotionPayment';
    case TransferFromUtility = 'TransferFromUtility';
}

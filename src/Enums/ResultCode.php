<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Enums;

/**
 * Common M-Pesa result codes returned in API responses and callbacks.
 */
enum ResultCode: int
{
    case Success = 0;
    case InsufficientFunds = 1;
    case LessThanMinimum = 2;
    case ExceedsMaximum = 3;
    case ExceedsDailyLimit = 4;
    case ExceedsPerTransactionLimit = 5;
    case InvalidDebitAccount = 6;
    case InvalidCreditAccount = 7;
    case UnresolvedDebitAccount = 8;
    case UnresolvedCreditAccount = 9;
    case DuplicateDetected = 10;
    case InternalError = 11;
    case UnresolvedDebitParty = 12;
    case UnresolvedCreditParty = 13;
    case SystemBusy = 15;
    case TransactionTimedOut = 17;
    case InvalidInitiator = 20;
    case TrafficBlocking = 26;
    case CancelledByUser = 1032;
    case RequestCancelledByUser = 1037;
    case TimeoutWaitingForResponse = 1036;
    case DSNotResponding = 1025;

    /**
     * Determine if this result code represents a successful transaction.
     */
    public function isSuccessful(): bool
    {
        return $this === self::Success;
    }

    /**
     * Get a human-readable description of this result code.
     */
    public function description(): string
    {
        return match ($this) {
            self::Success => 'The transaction was completed successfully.',
            self::InsufficientFunds => 'The account has insufficient funds.',
            self::LessThanMinimum => 'The amount is less than the minimum allowed.',
            self::ExceedsMaximum => 'The amount exceeds the maximum allowed.',
            self::ExceedsDailyLimit => 'The amount exceeds the daily transaction limit.',
            self::ExceedsPerTransactionLimit => 'The amount exceeds the per-transaction limit.',
            self::InvalidDebitAccount => 'The debit account is invalid.',
            self::InvalidCreditAccount => 'The credit account is invalid.',
            self::UnresolvedDebitAccount => 'The debit account could not be resolved.',
            self::UnresolvedCreditAccount => 'The credit account could not be resolved.',
            self::DuplicateDetected => 'A duplicate transaction was detected.',
            self::InternalError => 'An internal system error occurred.',
            self::UnresolvedDebitParty => 'The debit party could not be resolved.',
            self::UnresolvedCreditParty => 'The credit party could not be resolved.',
            self::SystemBusy => 'The system is currently busy. Please try again.',
            self::TransactionTimedOut => 'The transaction timed out.',
            self::InvalidInitiator => 'The initiator credentials are invalid.',
            self::TrafficBlocking => 'Traffic blocking condition detected.',
            self::CancelledByUser => 'The request was cancelled by the user.',
            self::RequestCancelledByUser => 'The STK Push request was cancelled by the user.',
            self::TimeoutWaitingForResponse => 'Timeout waiting for user response on STK Push.',
            self::DSNotResponding => 'The downstream service is not responding.',
        };
    }
}

<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Exceptions;

use Exception;

/**
 * Base exception for all M-Pesa related errors.
 *
 * All package exceptions extend this class, allowing consumers
 * to catch all M-Pesa errors with a single catch block.
 */
class MpesaException extends Exception
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
        protected readonly ?string $resultCode = null,
        protected readonly ?string $resultDescription = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the Daraja API result code, if available.
     */
    public function getResultCode(): ?string
    {
        return $this->resultCode;
    }

    /**
     * Get the Daraja API result description, if available.
     */
    public function getResultDescription(): ?string
    {
        return $this->resultDescription;
    }
}

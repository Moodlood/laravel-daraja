<?php

declare(strict_types=1);

namespace Moodlood\LaravelDaraja\Exceptions;

/**
 * Thrown when a network-level error occurs (connection refused, DNS failure, etc.).
 */
class NetworkException extends MpesaException {}

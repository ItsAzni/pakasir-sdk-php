<?php

declare(strict_types=1);

namespace ItsAzni\Pakasir\Exceptions;

use Exception;

/**
 * Base exception for Pakasir SDK errors
 */
class PakasirException extends Exception
{
    public function __construct(
        string $message = 'Pakasir SDK Error',
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}

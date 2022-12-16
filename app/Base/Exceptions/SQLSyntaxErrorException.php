<?php

namespace App\Base\Exceptions;

use Exception;

/**
 * This error is thrown when errors were found in the query
 * please check your SQL
 */
class SQLSyntaxErrorException extends Exception
{
    public function __construct(string $message, $code = 0000)
    {
        $this->message = "{$code}: {$message}";
    }
}

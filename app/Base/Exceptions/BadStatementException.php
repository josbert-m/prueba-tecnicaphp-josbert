<?php 

namespace App\Base\Exceptions;

use Exception;

/**
 * This exception is thown when pass invalid statement to SQL query
 */
class BadStatementException extends Exception
{
    public function __construct()
    {
        $this->message = 'Invalid statement: expected UPDATE|SELECT|INSERT|DELETE';
    }
}

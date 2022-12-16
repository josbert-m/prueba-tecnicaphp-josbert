<?php

namespace App\Base\Exceptions;

use Exception;

class NotFoundRecordException extends Exception
{
    public function __construct()
    {
        $this->message = 'This record is not found in Database';
    }
}

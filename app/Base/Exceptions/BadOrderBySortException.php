<?php

namespace App\Base\Exceptions;

use Exception;

/**
 * This exception is thown when the order by keyword is bad
 */
class BadOrderBySortException extends Exception
{
    public function __construct()
    {
        $this->message = 'Bad sort keyword: expected ASC|DESC';
    }
}

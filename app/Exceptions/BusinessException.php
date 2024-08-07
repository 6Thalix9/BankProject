<?php

namespace App\Exceptions;

use Exception;

class BusinessException extends Exception
{
    protected $statusCode;

    public function __construct($message = "", $statusCode = 201, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}

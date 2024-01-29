<?php

namespace App\Exceptions;

use RuntimeException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ServiceException extends RuntimeException
{
    public function __construct($message = null, int $statusCode = ResponseAlias::HTTP_BAD_REQUEST)
    {
        parent::__construct($message, $statusCode);
    }
}

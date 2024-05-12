<?php

namespace App\Exceptions\Http;


use App\Exceptions\Base\HttpException as HttpExceptionAlias;

class BadRequestException extends HttpExceptionAlias
{
    public function __construct($message = null)
    {
        parent::__construct(1007, get_class($this), $message, 400);
    }
}

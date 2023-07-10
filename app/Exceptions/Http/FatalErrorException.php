<?php

namespace App\Exceptions\Http;


use App\Exceptions\Base\HttpException;

class FatalErrorException extends HttpException
{

    public function __construct($message=null)
    {
        parent::__construct(
            1007,
            get_class_name($this),
            env('APP_DEBUG') == true ? $message : trans('app.' . get_class_name($this)),
            500
        );
    }
}

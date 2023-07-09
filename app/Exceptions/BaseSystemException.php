<?php

namespace App\Exceptions;

use Exception;

/**
 * Class BaseBaseSystemException
 * @package App\Exceptions\BaseSystemException
 */
abstract class BaseSystemException extends Exception
{
    use BaseExceptionTrait;
}

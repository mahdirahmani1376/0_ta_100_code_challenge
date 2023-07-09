<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Class BaseApplicationException
 * @package App\Exceptions\ApplicationException
 */
abstract class BaseApplicationException extends RuntimeException
{
    use BaseExceptionTrait;
}

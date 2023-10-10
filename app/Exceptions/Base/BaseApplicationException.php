<?php

namespace App\Exceptions\Base;

use RuntimeException;

/**
 * Class BaseApplicationException
 * @package App\Exceptions\ApplicationException
 */
abstract class BaseApplicationException extends RuntimeException
{
    use BaseExceptionTrait;
}

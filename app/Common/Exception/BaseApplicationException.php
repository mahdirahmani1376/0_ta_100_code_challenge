<?php

namespace App\Common\Exception;

use RuntimeException;

/**
 * Class BaseApplicationException
 * @package App\Exceptions\ApplicationException
 */
abstract class BaseApplicationException extends RuntimeException
{
    use BaseExceptionTrait;
}

<?php

namespace App\Common\Exception;

/**
 * Class ExceptionTypes
 * @package App\ValueObjects
 */
class ExceptionTypes
{
    public const TYPE_ACTION     = 1;

    public const TYPE_SERVICE    = 2;
    public const TYPE_REPOSITORY = 3;
    public const TYPE_MODEL      = 4;
    public const TYPE_EVENT      = 5;
    public const TYPE_JOB        = 6;
    public const TYPE_COMMAND    = 7;
    public const TYPE_DEBUG      = 8;
}

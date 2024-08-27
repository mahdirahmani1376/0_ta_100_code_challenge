<?php

namespace App\Exceptions\SystemException;

use App\Exceptions\Base\BaseApplicationException;
use App\Exceptions\Base\ExceptionCodes;
use App\Exceptions\Base\ExceptionTypes;

/**
 * Class AmountMustBeGreaterThanZeroException
 * @package App\Exceptions\SystemException
 * @method static self make()
 * @method self params()
 */
class AmountMustBeGreaterThanZeroException extends BaseApplicationException
{
    protected string $logRef = ExceptionCodes::AMOUNT_MUST_BE_GREATER_THAN_ZERO;

    protected int $errorCode = 403;

    protected int $logType = ExceptionTypes::TYPE_ACTION;

    protected array $messageParams = [];
}

<?php

namespace App\Exceptions\SystemException;


use App\Exceptions\Base\BaseApplicationException;
use App\Exceptions\Base\ExceptionCodes;
use App\Exceptions\Base\ExceptionTypes;

/**
 * Class ClientNotFoundException
 * @package App\Exceptions\ApplicationException
 * @method static self make()
 * @method self params()
 */
class NotEnoughCreditException extends BaseApplicationException
{
    protected string $logRef = ExceptionCodes::NOT_ENOUGH_CREDIT;

    protected int $errorCode = 403;

    protected int $logType = ExceptionTypes::TYPE_SERVICE;

    protected array $messageParams = [];
}

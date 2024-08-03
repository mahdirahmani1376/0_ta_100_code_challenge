<?php

namespace App\Exceptions\SystemException;


use App\Exceptions\Base\BaseApplicationException;
use App\Exceptions\Base\ExceptionCodes;
use App\Exceptions\Base\ExceptionTypes;

/**
 * Class UserNotFoundOnMainAppException
 * @package App\Exceptions\ApplicationException
 * @method static self make(int $invoice_id)
 * @method self params(int $invoice_id)
 */
class UserNotFoundOnMainAppException extends BaseApplicationException
{
    // Error code 410092
    protected string $logRef = ExceptionCodes::USER_NOT_FOUND_EXCEPTION;

    protected int $errorCode = 400;

    protected int $logType = ExceptionTypes::TYPE_SERVICE;

    protected array $messageParams = [
        'invoice_id' => '',
    ];
}

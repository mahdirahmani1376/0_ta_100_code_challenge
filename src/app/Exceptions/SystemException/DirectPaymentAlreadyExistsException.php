<?php

namespace App\Exceptions\SystemException;


use App\Exceptions\Base\BaseApplicationException;
use App\Exceptions\Base\ExceptionCodes;
use App\Exceptions\Base\ExceptionTypes;

/**
 * Class UserNotFoundOnMainAppException
 * @package App\Exceptions\DirectPaymentAlreadyExistsException
 * @method static self make(int $direct_payment_id)
 * @method self params(int $direct_payment_id)
 */
class DirectPaymentAlreadyExistsException extends BaseApplicationException
{
    // Error code 410099
    protected string $logRef = ExceptionCodes::DIRECT_PAYMENT_ALREADY_EXISTS_EXCEPTION;

    protected int $errorCode = 400;

    protected int $logType = ExceptionTypes::TYPE_SERVICE;

    protected array $messageParams = [
        'direct_payment_id' => '',
    ];
}

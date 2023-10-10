<?php

namespace App\Exceptions\SystemException;


use App\Exceptions\Base\BaseApplicationException;
use App\Exceptions\Base\ExceptionCodes;
use App\Exceptions\Base\ExceptionTypes;

/**
 * Class OfflinePaymentApplyException
 * @package App\Exceptions\ApplicationException
 * @method static self make($offline_payment_id)
 * @method self params($offline_payment_id)
 */
class OfflinePaymentApplyException extends BaseApplicationException
{
    protected string $logRef = ExceptionCodes::OFFLINE_PAYMENT_APPLY;

    protected int $errorCode = 403;

    protected int $logType = ExceptionTypes::TYPE_ACTION;

    protected array $messageParams = [
        "offline_payment_id" => ""
    ];
}

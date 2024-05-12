<?php

namespace App\Exceptions\SystemException;


use App\Exceptions\Base\BaseApplicationException;
use App\Exceptions\Base\ExceptionCodes;
use App\Exceptions\Base\ExceptionTypes;

/**
 * @method static self make(string $message)
 */
class MergeInvoiceException extends BaseApplicationException
{
    // Error code 410095
    protected string $logRef = ExceptionCodes::INVOICE_IS_CREDIT_OR_MASS_PAYMENT;

    protected int $errorCode = 400;

    protected int $logType = ExceptionTypes::TYPE_ACTION;

    protected array $messageParams = [
        'message' => '',
    ];
}

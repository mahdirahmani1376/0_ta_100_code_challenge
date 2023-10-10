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
class ApplyCreditToCreditInvoiceException extends BaseApplicationException
{
    protected string $logRef = ExceptionCodes::APPLY_CREDIT_TO_CREDIT_INVOICE_NOT_ALLOWED;

    protected int $errorCode = 403;

    protected int $logType = ExceptionTypes::TYPE_ACTION;

    protected array $messageParams = [];
}

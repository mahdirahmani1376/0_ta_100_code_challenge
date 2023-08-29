<?php

namespace App\Exceptions\SystemException;

use App\Exceptions\Base\BaseApplicationException;
use App\Exceptions\Base\ExceptionCodes;
use App\Exceptions\Base\ExceptionTypes;

/**
 * Class InvalidUserIdentifierException
 * @package App\Exceptions\ApplicationException
 * @method static self make(int $invoice_id)
 * @method self params(int $invoice_id)
 */
class InvoiceCancellationFailedException extends BaseApplicationException
{
    // Error code 420015
    protected string $logRef = ExceptionCodes::INVOICE_CANCELLATION_FAILED;

    protected int $errorCode = 403;

    protected int $logType = ExceptionTypes::TYPE_ACTION;

    protected array $messageParams = [
        'invoice_id' => ''
    ];
}

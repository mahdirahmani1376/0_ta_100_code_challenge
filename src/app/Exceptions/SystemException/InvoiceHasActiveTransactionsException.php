<?php

namespace App\Exceptions\SystemException;


use App\Exceptions\Base\BaseApplicationException;
use App\Exceptions\Base\ExceptionCodes;
use App\Exceptions\Base\ExceptionTypes;

/**
 * Class InvoiceHasActiveTransactionsException
 * @package App\Exceptions\ApplicationException
 * @method static self make(int $invoice_id)
 * @method self params(int $invoice_id)
 */
class InvoiceHasActiveTransactionsException extends BaseApplicationException
{
    // Error code 420032
    protected string $logRef = ExceptionCodes::INVOICE_HAS_ACTIVE_TRANSACTIONS;

    protected int $errorCode = 400;

    protected int $logType = ExceptionTypes::TYPE_SERVICE;

    protected array $messageParams = [
        'invoice_id'      => '',
    ];
}

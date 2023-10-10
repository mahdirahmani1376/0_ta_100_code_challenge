<?php

namespace App\Exceptions\SystemException;


use App\Exceptions\Base\BaseApplicationException;
use App\Exceptions\Base\ExceptionCodes;
use App\Exceptions\Base\ExceptionTypes;

/**
 * Class UpdatingPaidOrRefundedInvoiceNotAllowedException
 * @package App\Exceptions\ApplicationException
 * @method static self make(int $invoice_id, $status)
 * @method self params(int $invoice_id, $status)
 */
class UpdatingPaidOrRefundedInvoiceNotAllowedException extends BaseApplicationException
{
    // Error code 420029
    protected string $logRef = ExceptionCodes::UPDATING_PAID_OR_REFUNDED_INVOICE_NOT_ALLOWED;

    protected int $errorCode = 400;

    protected int $logType = ExceptionTypes::TYPE_SERVICE;

    protected array $messageParams = [
        'invoice_id' => '',
        'status'     => ''
    ];
}

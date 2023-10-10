<?php

namespace App\Exceptions\SystemException;


use App\Exceptions\Base\BaseApplicationException;
use App\Exceptions\Base\ExceptionCodes;
use App\Exceptions\Base\ExceptionTypes;

/**
 * Class SplittingAllInvoiceItemsNotAllowedException
 * @package App\Exceptions\ApplicationException
 * @method static self make(int $invoice_id)
 * @method self params(int $invoice_id)
 */
class AtLeastOneInvoiceItemMustRemainException extends BaseApplicationException
{
    // Error code 420031
    protected string $logRef = ExceptionCodes::AT_LEAST_ONE_INVOICE_ITEM_MUST_REMAIN;

    protected int $errorCode = 400;

    protected int $logType = ExceptionTypes::TYPE_SERVICE;

    protected array $messageParams = [
        'invoice_id'      => '',
    ];
}

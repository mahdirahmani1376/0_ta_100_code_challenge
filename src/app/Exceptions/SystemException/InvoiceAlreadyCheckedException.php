<?php

namespace App\Exceptions\SystemException;

use App\Exceptions\Base\BaseApplicationException;
use App\Exceptions\Base\ExceptionCodes;
use App\Exceptions\Base\ExceptionTypes;

/**
 * Class InvoiceAlreadyCheckedException
 * @package App\Exceptions\SystemException
 * @method static self make(int $invoice_id, int $admin_id)
 * @method self params(int $invoice_id, int $admin_id)
 */
class InvoiceAlreadyCheckedException extends BaseApplicationException
{
    protected string $logRef = ExceptionCodes::INVOICE_ALREADY_CHECKED;

    protected int $errorCode = 403;

    protected int $logType = ExceptionTypes::TYPE_ACTION;

    protected array $messageParams = [
        'invoice_id' => '',
        'admin_id'   => ''
    ];
}

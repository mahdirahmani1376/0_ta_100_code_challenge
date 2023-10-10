<?php

namespace App\Exceptions\SystemException;

use App\Exceptions\Base\BaseSystemException;
use App\Exceptions\Base\ExceptionCodes;
use App\Exceptions\Base\ExceptionTypes;

/**
 * Class InvoiceLockedAlreadyImportedToRahkaranException
 * @package App\Exceptions\SystemException
 * @method static self make(int $invoice_id)
 * @method self params(int $invoice_id)
 */
class InvoiceLockedAndAlreadyImportedToRahkaranException extends BaseSystemException
{
    // Error code 530073
    protected string $logRef = ExceptionCodes::LOCKED_INVOICE_ALREADY_IMPORTED_TO_RAHKARAN;

    protected int $logType = ExceptionTypes::TYPE_ACTION;

    protected int $errorCode = 500;

    protected array $messageParams = [
        'invoice_id' => '',
    ];
}

<?php

namespace App\Exceptions\SystemException;

use App\Exceptions\Base\BaseApplicationException;
use App\Exceptions\Base\ExceptionCodes;
use App\Exceptions\Base\ExceptionTypes;

/**
 * Class InvalidUserIdentifierException
 * @package App\Exceptions\ApplicationException
 * @method static self make(string $status)
 * @method self params(string $status)
 */
class UpdateStatusUnacceptableException extends BaseApplicationException
{
    // Error code 420015
    protected string $logRef = ExceptionCodes::INVOICE_STATUS_UNACCEPTABLE;

    protected int $errorCode = 400;

    protected int $logType = ExceptionTypes::TYPE_ACTION;

    protected array $messageParams = [
        'status' => ''
    ];
}

<?php

namespace App\Exceptions\SystemException;


use App\Exceptions\Base\BaseApplicationException;
use App\Exceptions\Base\ExceptionCodes;
use App\Exceptions\Base\ExceptionTypes;

/**
 * Class BazaarPayAPIException
 * @package App\Exceptions\ApplicationException
 * @method static self make(string $error, int $status_code, string $additional)
 * @method self params(string $error, int $status_code, string $additional)
 */
class BazaarPayAPIException extends BaseApplicationException
{
    // Error code 420029
    protected string $logRef = ExceptionCodes::BAZAAR_PAY_API;

    protected int $errorCode = 400;

    protected int $logType = ExceptionTypes::TYPE_SERVICE;

    protected array $messageParams = [
        'error' => '',
        'status_code' => '',
        'additional' => '',
    ];
}

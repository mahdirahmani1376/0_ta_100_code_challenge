<?php

namespace App\Exceptions\SystemException;


use App\Exceptions\Base\BaseApplicationException;
use App\Exceptions\Base\ExceptionCodes;
use App\Exceptions\Base\ExceptionTypes;

/**
 * Class ClientNotFoundException
 * @package App\Exceptions\ApplicationException
 * @method static self make(string $name)
 * @method self params(string $name)
 */
class MakeBankGatewayFailedException extends BaseApplicationException
{
    protected string $logRef = ExceptionCodes::MAKE_BANK_GATEWAY_FAILED;

    protected int $errorCode = 400;

    protected int $logType = ExceptionTypes::TYPE_SERVICE;

    protected array $messageParams = [
        'name' => ''
    ];
}

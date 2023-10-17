<?php

namespace App\Exceptions\SystemException;

use App\Exceptions\Base\BaseApplicationException;
use App\Exceptions\Base\ExceptionCodes;
use App\Exceptions\Base\ExceptionTypes;

/**
 * Class NotAuthorizedException
 * @package App\Exceptions\ApplicationException
 * @method static self make()
 * @method self params()
 */
class NotAuthorizedException extends BaseApplicationException
{
    protected string $logRef = ExceptionCodes::NOT_AUTHORIZED;

    protected int $errorCode = 403;

    protected int $logType = ExceptionTypes::TYPE_ACTION;

    protected array $messageParams = [];
}

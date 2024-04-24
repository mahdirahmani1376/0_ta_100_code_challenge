<?php

namespace App\Exceptions\SystemException;

use App\Exceptions\Base\BaseSystemException;
use App\Exceptions\Base\ExceptionCodes;
use App\Exceptions\Base\ExceptionTypes;

/**
 * Class MainAppInternalAPIException
 * @package App\Exceptions\SystemException
 * @method static self make($url,$param=null)
 * @method self params($url, $param=null)
 */
class MainAppInternalAPIException extends BaseSystemException
{
    // Error code 530073
    protected string $logRef = ExceptionCodes::MAIN_APP_INTERNAL_API;

    protected int $logType = ExceptionTypes::TYPE_SERVICE;

    protected int $errorCode = 500;

    protected array $messageParams = [
        'url' => '',
        'param' => '',
    ];
}

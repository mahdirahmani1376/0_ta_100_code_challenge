<?php

namespace App\Exceptions\Repository;

use App\Common\Exception\BaseSystemException;
use App\Common\Exception\ExceptionCodes;
use App\Common\Exception\ExceptionTypes;

/**
 * Class RepositoryModelNotFoundException
 * @package App\Exceptions\SystemException
 * @method static self make(string $model)
 * @method self params(string $model)
 */
class ModelNotFoundException extends BaseSystemException
{
    // Error code 530027
    protected string $logRef = ExceptionCodes::REPOSITORY_MODEL_NOT_FOUND;

    protected int $logType = ExceptionTypes::TYPE_REPOSITORY;

    protected int $errorCode = 404;

    protected array $messageParams = [
        'model' => ''
    ];
}

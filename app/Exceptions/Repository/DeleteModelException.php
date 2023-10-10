<?php

namespace App\Exceptions\Repository;

use App\Exceptions\Base\BaseSystemException;
use App\Exceptions\Base\ExceptionCodes;
use App\Exceptions\Base\ExceptionTypes;

/**
 * Class RepositoryDeleteModelException
 * @package App\Exceptions\SystemException
 * @method static self make(string $model, $id)
 * @method self params(string $model, int $id = null)
 */
class DeleteModelException extends BaseSystemException
{
    // Error code 530023
    protected string $logRef = ExceptionCodes::REPOSITORY_DELETE_MODEL;

    protected int $errorCode = 500;

    protected int $logType = ExceptionTypes::TYPE_REPOSITORY;

    protected array $messageParams = [
        'model' => '',
    ];
}

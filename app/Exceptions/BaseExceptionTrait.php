<?php

namespace App\Exceptions;

use App\Exceptions\ApplicationException\BaseApplicationException;
use App\Exceptions\SystemException\BaseSystemException;
use Exception;
use Throwable;

/**
 * Trait BaseExceptionTrait
 * @package App\Traits
 * @mixin Exception
 * @property int $errorCode
 * @property int|string $logRef
 * @property array $messageParams
 * @parent Exception
 */
trait BaseExceptionTrait
{
    protected int $errorCode; // 4xx and 5xx Http error codes are allowed

    protected string $logRef;

    protected ?array $messages;

    protected array $messageParams = [];

    abstract public function __construct($message = '', $code = 0, Throwable $previous = null);

    public function getLogRef()
    {
        return $this->logRef;
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }

    public function getMessageParams(): array
    {
        return $this->messageParams;
    }

    public function setMessageParams(array $params)
    {
        foreach (array_keys($this->messageParams) as $key) {
            $this->messageParams[$key] = array_shift($params);
        }
    }

    /**
     * @param ...$args
     * @return $this|BaseSystemException|BaseApplicationException
     */
    public static function make(...$args)
    {
        $exception = new static();
        $exception->code = $exception->getErrorCode();
        $exception->setMessageParams($args);
        $exception->message = trans('exceptions.' . $exception->getLogRef(), $exception->getMessageParams());

        return $exception;
    }

    /**
     * @param Throwable $exception
     * @return $this|mixed
     */
    public static function throw(Throwable $exception)
    {
        $base_exception = new static(
            '',
            0,
            $exception
        );

        $base_exception->code = $base_exception->getErrorCode();

        return $base_exception;
    }

    public function params(...$args): self
    {
        $this->setMessageParams($args);
        $this->message = trans('exceptions.' . $this->getLogRef(), $this->getMessageParams());

        return $this;
    }
}

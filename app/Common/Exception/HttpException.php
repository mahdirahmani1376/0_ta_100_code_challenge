<?php

namespace App\Common\Exception;

use RuntimeException;
use Throwable;

/**
 * Class HttpException
 * @package App\Exceptions\HttpException
 */
Abstract class HttpException extends RuntimeException
{
    /**
     * @var int
     */
    protected $logRef;

    /**
     * @var string
     */
    protected $errorCode;

    /**
     * HttpException constructor.
     * @param int $logRef
     * @param string $errorCode
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        int $logRef,
        string $errorCode,
        string $message = '',
        int $code = 0,
        Throwable $previous = null
    ) {
        $this->logRef = $logRef;
        $this->errorCode = $errorCode;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return int
     */
    public function getLogRef()
    {
        return $this->logRef;
    }

    /**
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }
}

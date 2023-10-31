<?php

namespace App\Exceptions;

use App\Exceptions\Base\BaseApplicationException;
use App\Exceptions\Base\BaseSystemException;
use App\Exceptions\Base\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof BaseSystemException) {
            return $this->generateJsonResponseForSystemException($e);
        } elseif ($e instanceof BaseApplicationException) {
            return $this->generateJsonResponseForApplicationException($e);
        } elseif ($e instanceof HttpException) {
            return $this->generateJsonResponseForHttpException($e);
        }

        return parent::render($request, $e);
    }

    protected function generateJsonResponseForSystemException(BaseSystemException $exception): JsonResponse
    {
        return response()->json([
            'code' => $exception->getCode(),
            'logRef' => $exception->getLogRef(),
            'errorCode' => $exception->getErrorCode(),
            'message' => __('exceptions.' . $exception->getLogRef(), $exception->getMessageParams())
        ], $exception->getCode());
    }
    protected function generateJsonResponseForApplicationException(BaseApplicationException $exception): JsonResponse
    {
        return response()->json([
            'code' => $exception->getCode(),
            'logRef' => $exception->getLogRef(),
            'errorCode' => $exception->getErrorCode(),
            'message' => __('exceptions.' . $exception->getLogRef(), $exception->getMessageParams())
        ], $exception->getCode());
    }
    protected function generateJsonResponseForHttpException(HttpException $exception): JsonResponse
    {
        return response()->json([
            'code' => $exception->getCode(),
            'logRef' => $exception->getLogRef(),
            'errorCode' => $exception->getErrorCode(),
            'message' => __($exception->getMessage())
        ], $exception->getCode());
    }
}

<?php

namespace App\Services;

use App\Jobs\ChangeLogJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class GatewayLogService
{
    public const ADMIN_TYPE = 'admin';
    public const CLIENT_TYPE = 'client';
    private ?Model $model = null;
    private array $after = [];
    private array $before = [];
    private null|string $logId = null;
    private string $userType = 'admin';
    private null|string $action = null;
    private bool $debugMode = false;
    private array $debugTrace = [];


    public function init(Request $request)
    {
        $adminLogId = $request->header('X-ADMIN-LOG-ID');
        $clientLogId = $request->header('X-CLIENT-LOG-ID');
        $debugMode = $request->header('X-DEBUG-MODE');

        if ($debugMode == 1) {
            $this->setAsDebugMode();
        }

        if ($adminLogId) {
            $this->asAdmin();
            $this->setLogId($adminLogId);
        } elseif ($clientLogId) {
            $this->asClient();
            $this->setLogId($clientLogId);
        }
        return $this;
    }

    public function setAsDebugMode()
    {
        $this->debugMode = true;
    }


    public function asAdmin()
    {
        $this->userType = self::ADMIN_TYPE;
        return $this;
    }

    public function setLogId($logId = null)
    {
        $this->logId = $logId;
        return $this;
    }

    public function asClient()
    {
        $this->userType = self::CLIENT_TYPE;
        return $this;
    }

    public function setModel(Model $model)
    {
        if (!isset($this->model)) {
            $this->model = $model;
        }
        return $this;
    }

    public function setBefore()
    {
        $this->before = $this->model->toArray();
        return $this;
    }

    public function toArray()
    {
        return [
            'model'    => isset($this->model) ? $this->model : null,
            'logId'    => isset($this->logId) ? $this->logId : null,
            'userType' => isset($this->userType) ? $this->userType : null,
            'action'   => isset($this->action) ? $this->action : null,
            'after'    => isset($this->after) ? $this->after : null,
            'before'   => isset($this->before) ? $this->before : null
        ];
    }

    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    public function dispatch()
    {
        $this->setChanges();

        if (
            !empty($this->logId)
            &&
            !empty($this->userType)
            &&
            !empty($this->action)
        ) {
            ChangeLogJob::dispatch(
                logId: $this->logId,
                action: $this->action,
                before: $this->before,
                after: $this->after,
                userType: $this->userType,
                model: $this->model
            );
        }
    }

    public function setChanges()
    {
        if (isset($this->model)) {
            $this->after = $this->model->getChanges();
        }
        return $this;
    }

    public function setResponse($response)
    {
        try {
            if ($this->isDebugMode()) {
                $content = json_decode($response->content(), true);
                $debugTraceData = ['debug_trace' => $this->getDebugTrace()];

                if (json_last_error() == JSON_ERROR_NONE) {
                    $response->setContent(json_encode(array_merge(
                        $content,
                        $debugTraceData
                    )));
                }
            }
        } catch (\Throwable $exception) {
            \Log::warning('Set debug trace data failed', $exception->getTrace());
        }

        return $response;
    }

    private function isDebugMode()
    {
        return $this->debugMode;
    }

    public function addDebugContext(string $key, $data)
    {
        if ($this->debugMode) {
            $this->debugTrace[$key] = $data;
        }
        return $this;
    }

    public function getDebugTrace()
    {
        return $this->debugTrace;
    }
}

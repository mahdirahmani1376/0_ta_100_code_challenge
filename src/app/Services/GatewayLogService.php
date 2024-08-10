<?php

namespace App\Services;

use App\Jobs\ChangeLogJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GatewayLogService
{
    private ?Model $model = null;
    private array $after = [];
    private array $before = [];
    private null|string $logId = null;
    private string $userType = 'admin';
    private array $debugTrace = [];
    private bool $debugMode = false;
    private null|string $action = null;

    public const ADMIN_TYPE = 'admin';
    public const CLIENT_TYPE = 'client';

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

    public function setAsDebugMode(): void
    {
        $this->debugMode = true;
    }

    public function isDebugMode(): bool
    {
        return $this->debugMode;
    }

    public function asAdmin(): static
    {
        $this->userType = self::ADMIN_TYPE;
        return $this;
    }

    public function asClient(): static
    {
        $this->userType = self::CLIENT_TYPE;
        return $this;
    }

    public function setLogId($logId = null): static
    {
        $this->logId = $logId;
        return $this;
    }

    public function setModel(Model $model): static
    {
        if (!isset($this->model)) {
            $this->model = $model;
        }
        return $this;
    }

    public function setChanges(): static
    {
        if (isset($this->model)) {
            $this->after = $this->model->getChanges();
        }
        return $this;
    }

    public function setBefore(): static
    {
        $this->before = $this->model->getOriginal();
        return $this;
    }

    public function setAction($action): static
    {
        $this->action = $action;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'model'       => isset($this->model) ? $this->model : null,
            'logId'       => isset($this->logId) ? $this->logId : null,
            'userType'    => isset($this->userType) ? $this->userType : null,
            'action'      => isset($this->action) ? $this->action : null,
            'after'       => isset($this->after) ? $this->after : null,
            'before'      => isset($this->before) ? $this->before : null,
            'debug_mode'  => isset($this->debugMode) ? $this->debugMode : null,
            'debug_trace' => isset($this->debugTrace) ? $this->debugTrace : []
        ];
    }

    public function dispatch(): void
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

    public function addDebugContext(string $key, $data): static
    {
        if ($this->debugMode) {
            $this->debugTrace[$key] = $data;
        }
        return $this;
    }

    public function getDebugTrace(): array
    {
        return $this->debugTrace;
    }

    public function setResponse(Response $response): Response
    {
        try {
            if ($this->isDebugMode()) {
                $content = json_decode($response->content(), true);
                $debugTraceData = ['debug_trace' => $this->debugTrace];

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

        $this->dispatch();

        return $response;
    }
}

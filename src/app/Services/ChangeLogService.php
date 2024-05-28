<?php

namespace App\Services;

use App\Jobs\ChangeLogJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ChangeLogService
{
    public const ADMIN_TYPE = 'admin';
    public const CLIENT_TYPE = 'client';
    private ?Model $model = null;
    private array $after = [];
    private array $before = [];
    private null|string $logId = null;
    private string $userType = 'admin';
    private null|string $action = null;

    public function init(Request $request)
    {
        $adminLogId = $request->get('admin_log_id');
        $clientLogId = $request->get('client_log_id');
        if ($adminLogId) {
            $this->asAdmin();
            $this->setLogId($adminLogId);
        } elseif ($clientLogId) {
            $this->asClient();
            $this->setLogId($clientLogId);
        }
        return $this;
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
}

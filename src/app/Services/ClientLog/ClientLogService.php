<?php

namespace App\Services\ClientLog;

use App\Models\ClientLog;
use MongoDB\Laravel\Eloquent\Model;

class ClientLogService
{
    public function updateLog(
        $logId,
        ?Model $model,
        $action = null
    )
    {
        $logModel = ClientLog::find($logId);

        if ($logModel) {
            $logModel->update($logModel, [
                'action'        => $action,
                'loggable_id'   => $model?->getKey(),
                'loggable_type' => $model?->getMorphClass()
            ]);
        }
    }
}
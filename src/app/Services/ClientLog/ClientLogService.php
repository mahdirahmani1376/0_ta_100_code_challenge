<?php

namespace App\Services\ClientLog;

use App\Models\ClientLog;
use Illuminate\Database\Eloquent\Model;

class ClientLogService
{
    public function updateLog(
        $logId,
        ?Model $model,
        $before = [],
        $after = [],
        $action = null
    )
    {
        $logModel = ClientLog::find($logId);

        if ($logModel) {
            $logModel->update([
                'action'        => $action,
                'logable_id'   => $model?->getKey(),
                'logable_type' => $model?->getMorphClass()
            ]);
        }
    }
}
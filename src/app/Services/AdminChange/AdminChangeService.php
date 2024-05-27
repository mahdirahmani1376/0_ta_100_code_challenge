<?php

namespace App\Services\AdminChange;

use App\Models\AdminLog;
use MongoDB\Laravel\Eloquent\Model;

class AdminChangeService
{
    public function updateLog(
        $logId,
        ?Model $model,
        $before = [],
        $after = [],
        $action = null
    )
    {
        $logModel = AdminLog::find($logId);

        if ($logModel) {
            $changes = $this->getDiff($before, $after);
            $logModel->update($logModel, [
                ...$changes,
                'action'        => $action,
                'loggable_id'   => $model?->getKey(),
                'loggable_type' => $model?->getMorphClass()
            ]);
        }
    }

    private function getDiff(mixed $old, mixed $changes)
    {
        $before = array_diff_assoc_rec($old,$changes);
        $after = array_diff_assoc_rec($changes,$old);

        $before = collect($before)->only(array_keys($after))->toArray();

        return collect([
            'before' => $before,
            'after'  => $after,
        ]);
    }

}
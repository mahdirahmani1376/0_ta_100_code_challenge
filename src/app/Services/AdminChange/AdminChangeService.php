<?php

namespace App\Services\AdminChange;

use App\Models\AdminChange;
use Illuminate\Database\Eloquent\Model;

class AdminChangeService
{
    public function updateLog(
        $logId,
        ?Model $model,
        $before = [],
        $after = [],
        $action = null
    ): void
    {
        $logModel = AdminChange::find($logId);

        if ($logModel) {
            $changes = $this->getDiff($before, $after);
            $logModel->update([
                ...$changes,
                'action'        => $action,
                'logable_id'   => $model?->getKey(),
                'logable_type' => $model?->getMorphClass()
            ]);
        }
    }

    private function getDiff(mixed $old, mixed $changes)
    {
        $before = array_diff_assoc_recursive($old, $changes);
        $after = array_diff_assoc_recursive($changes, $old);

        $before = collect($before)->only(array_keys($after))->toArray();

        return collect([
            'before' => $before,
            'after'  => $after,
        ]);
    }

}
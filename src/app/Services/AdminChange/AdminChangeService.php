<?php

namespace App\Services\AdminChange;

use App\Models\AdminChange;
use Illuminate\Database\Eloquent\Model;
use Log;
use Throwable;

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
        try {
            $logModel = AdminChange::findOrFail($logId);

            if ($logModel) {
                $changes = $this->getDiff($before, $after);
                $logModel->update([
                    ...$changes,
                    'action'       => $action,
                    'logable_id'   => $model?->getKey(),
                    'logable_type' => $model?->getMorphClass()
                ]);
            }
        } catch (Throwable $exception) {
            Log::error('Update admin change failed', $exception->getTrace());
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
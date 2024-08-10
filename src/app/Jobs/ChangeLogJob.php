<?php

namespace App\Jobs;

use App\Services\AdminChange\AdminChangeService;
use App\Services\ClientLog\ClientLogService;
use App\Services\GatewayLogService;
use App\ValueObjects\Queue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class ChangeLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var AdminChangeService|ClientLogService|mixed
     */
    private AdminChangeService|ClientLogService $logService;

    public function __construct(
        private readonly string $logId,
        private readonly string $action,
        private readonly array  $before,
        private readonly array  $after,
        private readonly string $userType = GatewayLogService::ADMIN_TYPE,
        private readonly ?Model $model = null,
    )
    {
        $this->onQueue(Queue::DEFAULT_QUEUE);

        if ($userType == GatewayLogService::ADMIN_TYPE) {
            $this->logService = App::make(AdminChangeService::class);
        } elseif ($userType == GatewayLogService::CLIENT_TYPE) {
            $this->logService = App::make(ClientLogService::class);
        }
    }

    public function handle()
    {
        try {
            $this->logService->updateLog(
                logId: $this->logId,
                model: $this->model,
                before: $this->before,
                after: $this->after,
                action: $this->action
            );
        } catch (\Throwable $exception) {
            \Log::warning("Update change log job failed", [
                'message' => $exception->getMessage()
            ]);
        }
    }
}

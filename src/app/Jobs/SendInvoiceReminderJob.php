<?php

namespace App\Jobs;

use App\Enums\QueueEnum;
use App\Integrations\MainApp\MainAppAPIService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class SendInvoiceReminderJob
 * @package App\Jobs
 * @method static PendingDispatch dispatch(array $payload, string $channel)
 * this job is used to send invoice reminders via main_application
 */
class SendInvoiceReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public function __construct(public readonly array $payload, public readonly string $channel)
    {
        $this->onQueue(QueueEnum::PROCESS_INVOICE_REMINDER);
    }

    public function handle(): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        MainAppAPIService::sendInvoiceReminder($this->payload, $this->channel);
    }
}

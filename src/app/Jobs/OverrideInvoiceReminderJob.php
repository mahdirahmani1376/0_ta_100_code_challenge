<?php

namespace App\Jobs;

use App\Enums\QueueEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

/**
 * Class OverrideInvoiceReminderJob
 * @package App\Jobs
 * @method static PendingDispatch dispatch(int $invoiceId)
 * this job is used to send invoice reminders
 */
class OverrideInvoiceReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public const DEFAULT_QUEUE = QueueEnum::PROCESS_INVOICE_NUMBER;


    public function __construct(private readonly int $invoiceId)
    {
    }

    public function handle(): void
    {
        Artisan::call('cron:invoice-reminder', [
            '--override-invoice-id' => $this->invoiceId,
        ]);
    }
}

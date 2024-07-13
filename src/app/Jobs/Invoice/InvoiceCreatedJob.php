<?php

namespace App\Jobs\Invoice;

use App\Enums\QueueEnum;
use App\Helpers\JalaliCalender;
use App\Integrations\MainApp\MainAppAPIService;
use App\Integrations\MainApp\MainAppConfig;
use App\Models\Invoice;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Throwable;

/**
 * this job dispatched after invoice created
 * send notification to owner
 */
class InvoiceCreatedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    private Invoice $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
        $this->onQueue(QueueEnum::PROCESS_INVOICE);
    }

    public function tags(): array
    {
        return ['invoice-create-notification:' . $this->invoice->id];
    }

    public function handle(): void
    {
        try {
            MainAppAPIService::sendInvoiceCreateEmail($this->invoice);
        } catch (Throwable $exception) {
            $this->fail($exception);
            Log::warning('Send notification to invoice has been failed!', $exception->getTrace());
        }
    }
}

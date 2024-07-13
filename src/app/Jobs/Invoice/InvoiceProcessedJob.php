<?php

namespace App\Jobs\Invoice;

use App\Enums\QueueEnum;
use App\Integrations\MainApp\MainAppAPIService;
use App\Models\Invoice;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

/**
 * this job dispatched after invoice paid and signal to gateway for process invoice items
 */
class InvoiceProcessedJob implements ShouldQueue
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
        return ['invoice-processed:' . $this->invoice->id];
    }

    public function handle(): void
    {
        try {
            MainAppAPIService::invoicePostProcess($this->invoice);
        } catch (\Throwable $exception) {
            $this->fail($exception);
            Log::warning('Send invoice signal to gateway has been failed!', $exception->getTrace());
        }
    }
}

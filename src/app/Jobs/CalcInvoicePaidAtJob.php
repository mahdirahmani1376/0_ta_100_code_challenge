<?php

namespace App\Jobs;

use App\Enums\QueueEnum;
use App\Models\Invoice;
use App\Services\Invoice\AssignInvoiceNumberService;
use App\Services\Invoice\CalcInvoicePaidAtService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalcInvoicePaidAtJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Invoice $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice->withoutRelations();
        $this->onQueue(QueueEnum::PROCESS_INVOICE);
    }

    public function handle(CalcInvoicePaidAtService $service): void
    {
        $service($this->invoice, true);
    }

    public function tags(): array
    {
        return ['calc-invoice-paid-at:' . $this->invoice->id];
    }
}

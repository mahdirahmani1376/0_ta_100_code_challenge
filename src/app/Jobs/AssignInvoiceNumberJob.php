<?php

namespace App\Jobs;

use App\Enums\QueueEnum;
use App\Models\Invoice;
use App\Services\Invoice\AssignInvoiceNumberService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class AssignInvoiceNumberJob
 * @package App\Jobs
 * @method static PendingDispatch dispatch(Invoice $invoice)
 * this job gets dispatched when Invoice is processing to assign an invoice number
 */
class AssignInvoiceNumberJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public const DEFAULT_QUEUE = QueueEnum::PROCESS_INVOICE_NUMBER;

    public Invoice $invoice;

    public $uniqueFor = 60;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice->withoutRelations();
    }

    public function handle(AssignInvoiceNumberService $assignInvoiceNumberService): void
    {
        $assignInvoiceNumberService($this->invoice);
    }

    public function uniqueId()
    {
        return $this->invoice->id;
    }
}

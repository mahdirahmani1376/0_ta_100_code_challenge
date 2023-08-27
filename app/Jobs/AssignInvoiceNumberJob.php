<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Services\Admin\Invoice\AssignInvoiceNumberService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AssignInvoiceNumberJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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

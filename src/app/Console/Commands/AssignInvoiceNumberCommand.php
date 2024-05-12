<?php

namespace App\Console\Commands;

use App\Services\Invoice\AssignInvoiceNumberService;
use App\Services\Invoice\FindInvoiceByIdService;
use Illuminate\Console\Command;

/**
 * This is for dev/debugging purposes only
 */
class AssignInvoiceNumberCommand extends Command
{
    protected $signature = 'app:assign-invoice-number {invoice*}';

    protected $description = 'Assign InvoiceNumber to one or more Invoice(s)';

    public function handle(AssignInvoiceNumberService $assignInvoiceNumberService, FindInvoiceByIdService $findInvoiceByIdService)
    {
        $invoiceIds = $this->argument('invoice');
        $this->alert('Assign InvoiceNumber to Invoice, count: ' . count($invoiceIds));

        $this->withProgressBar($invoiceIds, function ($invoiceId) use ($assignInvoiceNumberService, $findInvoiceByIdService) {
            $invoice = $findInvoiceByIdService($invoiceId);
            $assignInvoiceNumberService($invoice);
        });

        $this->newLine();
    }
}

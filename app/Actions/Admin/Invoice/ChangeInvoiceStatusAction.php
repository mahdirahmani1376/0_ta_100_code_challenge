<?php

namespace App\Actions\Admin\Invoice;

use App\Models\Invoice;
use App\Services\Admin\Invoice\ChangeInvoiceStatusService;
use App\Services\Invoice\CalcInvoicePaidAtService;

class ChangeInvoiceStatusAction
{
    public function __construct(
        private readonly ChangeInvoiceStatusService $changeInvoiceStatusService,
        private readonly CalcInvoicePaidAtService   $calcInvoicePaidAtService
    )
    {
    }

    public function __invoke(Invoice $invoice, string $status): Invoice
    {
        // TODO AdminLog
        check_rahkaran($invoice);

        $invoice = ($this->changeInvoiceStatusService)($invoice, $status);
        // TODO Run ProcessInvoiceAction instead if its paid
        // TODO run CancelInvoiceAction if its cancelled
        return ($this->calcInvoicePaidAtService)($invoice);
    }
}

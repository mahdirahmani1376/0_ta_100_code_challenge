<?php

namespace App\Actions\Admin\Invoice;

use App\Models\Invoice;
use App\Services\Admin\Invoice\ChangeInvoiceStatusService;
use App\Services\Invoice\CalcInvoicePaidAtService;

class ChangeInvoiceStatusAction
{
    private ChangeInvoiceStatusService $changeInvoiceStatusService;
    private CalcInvoicePaidAtService $calcInvoicePaidAtService;

    public function __construct(ChangeInvoiceStatusService $changeInvoiceStatusService,
                                CalcInvoicePaidAtService   $calcInvoicePaidAtService)
    {
        $this->changeInvoiceStatusService = $changeInvoiceStatusService;
        $this->calcInvoicePaidAtService = $calcInvoicePaidAtService;
    }

    public function __invoke(Invoice $invoice, string $status): Invoice
    {
        // TODO AdminLog
        check_rahkaran($invoice);

        $invoice = ($this->changeInvoiceStatusService)($invoice, $status);

        return ($this->calcInvoicePaidAtService)($invoice);
    }
}

<?php

namespace App\Actions\Invoice;

use App\Models\Invoice;
use App\Services\Invoice\CancelInvoiceService;

class CancelInvoiceAction
{
    public function __construct(
        private readonly CancelInvoiceService $cancelInvoiceService,
    )
    {
    }

    public function __invoke(Invoice $invoice)
    {
        check_rahkaran($invoice);

        if (!in_array($invoice->status, [Invoice::STATUS_UNPAID, Invoice::STATUS_DRAFT])) {
            return $invoice;
        }

        return ($this->cancelInvoiceService)($invoice);
    }
}

<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;

class ProcessInvoiceService
{
    public function __construct(private readonly InvoiceRepositoryInterface $invoiceRepository)
    {
    }

    public function __invoke(Invoice $invoice, $shouldProcessPaidInvoice = false): Invoice
    {
        if (
            $shouldProcessPaidInvoice &&
            in_array($invoice->status, [Invoice::STATUS_UNPAID, Invoice::STATUS_COLLECTIONS, Invoice::STATUS_PAYMENT_PENDING]) &&
            $invoice->balance == 0 &&
            $invoice->total > 0
        ) {
            // TODO dispatch paid invoice job
            $invoice = $this->invoiceRepository->update($invoice, ['status' => Invoice::STATUS_PAID,], ['status',]);
        }

        check_rahkaran($invoice);

        return $invoice;
    }
}

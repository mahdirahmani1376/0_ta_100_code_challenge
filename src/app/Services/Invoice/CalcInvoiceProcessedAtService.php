<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;

class CalcInvoiceProcessedAtService
{
    public function __construct(private readonly InvoiceRepositoryInterface $invoiceRepository)
    {
    }

    public function __invoke(Invoice $invoice)
    {
        return $this->invoiceRepository->update($invoice, ['processed_at' => now()], ['processed_at']);
    }
}

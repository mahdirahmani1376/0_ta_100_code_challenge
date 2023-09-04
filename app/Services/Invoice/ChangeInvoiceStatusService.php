<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;

class ChangeInvoiceStatusService
{
    public function __construct(private readonly InvoiceRepositoryInterface $invoiceRepository)
    {
    }

    public function __invoke(Invoice $invoice, string $status): Invoice
    {
        $this->invoiceRepository->update($invoice, ['status' => $status,], ['status',]);

        return $invoice;
    }
}

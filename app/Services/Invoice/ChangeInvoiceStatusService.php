<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;

class ChangeInvoiceStatusService
{
    private InvoiceRepositoryInterface $invoiceRepository;

    public function __construct(InvoiceRepositoryInterface $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
    }

    public function __invoke(Invoice $invoice, string $status): Invoice
    {
        $this->invoiceRepository->update($invoice, ['status' => $status,], ['status',]);

        return $invoice;
    }
}

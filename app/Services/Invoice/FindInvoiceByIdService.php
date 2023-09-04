<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;

class FindInvoiceByIdService
{
    public function __construct(private readonly InvoiceRepositoryInterface $invoiceRepository)
    {
    }

    public function __invoke(int $invoiceId): Invoice
    {
        return $this->invoiceRepository->find($invoiceId);
    }
}

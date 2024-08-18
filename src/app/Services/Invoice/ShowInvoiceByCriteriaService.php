<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;

class ShowInvoiceByCriteriaService
{
    public function __construct(
        private readonly InvoiceRepositoryInterface $invoiceRepository,
    )
    {
    }

    public function __invoke(array $criteria = [], $throwException = false): ?Invoice
    {
        return $this->invoiceRepository->findOneByCriteria($criteria, $throwException);
    }
}

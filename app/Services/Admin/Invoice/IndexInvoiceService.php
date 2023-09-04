<?php

namespace App\Services\Admin\Invoice;

use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;

class IndexInvoiceService
{
    public function __construct(private readonly InvoiceRepositoryInterface $invoiceRepository)
    {
    }

    public function __invoke(array $data)
    {
        return $this->invoiceRepository->adminIndex($data);
    }
}

<?php

namespace App\Services\Invoice;

use App\Repositories\Invoice\Interface\InvoiceNumberRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexInvoiceNumberService
{
    public function __construct(private readonly InvoiceNumberRepositoryInterface $invoiceNumberRepository)
    {
    }

    public function __invoke(array $data): LengthAwarePaginator
    {
        return $this->invoiceNumberRepository->index($data);
    }
}

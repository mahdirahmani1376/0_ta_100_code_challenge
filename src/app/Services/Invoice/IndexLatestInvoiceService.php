<?php

namespace App\Services\Invoice;

use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class IndexLatestInvoiceService
{
    public function __construct(private readonly InvoiceRepositoryInterface $invoiceRepository)
    {
    }

    public function __invoke($profileId): LengthAwarePaginator|Collection
    {
        return $this->invoiceRepository->getLatestInvoices($profileId);
    }
}

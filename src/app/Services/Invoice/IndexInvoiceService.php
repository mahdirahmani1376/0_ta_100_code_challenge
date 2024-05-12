<?php

namespace App\Services\Invoice;

use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class IndexInvoiceService
{
    public function __construct(private readonly InvoiceRepositoryInterface $invoiceRepository)
    {
    }

    public function __invoke(array $data): LengthAwarePaginator|Collection
    {
        return $this->invoiceRepository->index($data);
    }
}

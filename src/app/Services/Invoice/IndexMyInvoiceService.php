<?php

namespace App\Services\Invoice;

use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IndexMyInvoiceService
{
    public function __construct(private readonly InvoiceRepositoryInterface $invoiceRepository)
    {
    }

    public function __invoke(array $data): LengthAwarePaginator
    {
        return $this->invoiceRepository->internalIndexMyInvoice($data);
    }
}

<?php

namespace App\Services\Profile\Invoice;

use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IndexInvoiceService
{
    public function __construct(private readonly InvoiceRepositoryInterface $invoiceRepository)
    {
    }

    public function __invoke(array $data): LengthAwarePaginator
    {
        return $this->invoiceRepository->profileIndex($data);
    }
}

<?php

namespace App\Services\Invoice;

use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use Illuminate\Support\Collection;

class IndexInvoiceService
{
    public function __construct(private readonly InvoiceRepositoryInterface $invoiceRepository)
    {
    }

    public function __invoke(array $data): Collection
    {
        return $this->invoiceRepository->internalIndex($data);
    }
}

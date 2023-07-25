<?php

namespace App\Services\Admin\Invoice;

use App\Repositories\Invoice\Interface\InvoiceNumberRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexInvoiceNumberService
{
    private InvoiceNumberRepositoryInterface $invoiceNumberRepository;

    public function __construct(InvoiceNumberRepositoryInterface $invoiceNumberRepository)
    {
        $this->invoiceNumberRepository = $invoiceNumberRepository;
    }

    public function __invoke(array $data): LengthAwarePaginator
    {
        return $this->invoiceNumberRepository->adminIndex($data);
    }
}

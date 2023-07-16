<?php

namespace App\Services\Admin\Invoice;

use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;

class IndexInvoiceService
{
    private InvoiceRepositoryInterface $invoiceRepository;

    public function __construct(
        InvoiceRepositoryInterface $invoiceRepository,
    )
    {
        $this->invoiceRepository = $invoiceRepository;
    }

    public function __invoke(array $data, array $paginationParam)
    {
        return $this->invoiceRepository->adminIndex($data, $paginationParam);
    }
}

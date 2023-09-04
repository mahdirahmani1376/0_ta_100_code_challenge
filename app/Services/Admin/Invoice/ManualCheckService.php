<?php

namespace App\Services\Admin\Invoice;

use App\Models\Invoice;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;

class ManualCheckService
{
    public function __construct(private readonly InvoiceRepositoryInterface $invoiceRepository)
    {
    }

    public function __invoke(Invoice $invoice, int $adminId): Invoice
    {
        return $this->invoiceRepository->update($invoice, ['admin_id' => $adminId], ['admin_id']);
    }
}

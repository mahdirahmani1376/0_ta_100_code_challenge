<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;

class ChangeInvoiceStatusService
{
    public function __construct(private readonly InvoiceRepositoryInterface $invoiceRepository)
    {
    }

    public function __invoke(Invoice $invoice, string $status): Invoice
    {
        $data = ['status' => $status];
        if ($invoice->status === Invoice::STATUS_UNPAID && is_null($invoice->paid_at)) {
            $data['paid_at'] = now();
        }

        return $this->invoiceRepository->update($invoice, $data, array_keys($data));
    }
}

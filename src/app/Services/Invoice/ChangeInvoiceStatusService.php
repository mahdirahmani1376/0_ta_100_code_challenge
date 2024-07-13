<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;

class ChangeInvoiceStatusService
{
    public function __construct(
        private readonly InvoiceRepositoryInterface $invoiceRepository,
        private readonly CalcInvoiceProcessedAtService $calcInvoiceProcessedAtService
    )
    {
    }

    public function __invoke(Invoice $invoice, string $status): Invoice
    {
        $data = ['status' => $status];
        if ($status === Invoice::STATUS_COLLECTIONS && is_null($invoice->paid_at)) {
            $data['paid_at'] = now();
            ($this->calcInvoiceProcessedAtService)($invoice);
        }

        return $this->invoiceRepository->update($invoice, $data, array_keys($data));
    }
}

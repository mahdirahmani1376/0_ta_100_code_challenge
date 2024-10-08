<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;

class UpdateInvoiceService
{
    public function __construct(private readonly InvoiceRepositoryInterface $invoiceRepository)
    {
    }

    public function __invoke(Invoice $invoice, array $data): Invoice
    {
        if (isset($data['invoice_date'])) {
            $data['created_at'] = $data['invoice_date'];
        }

        return $this->invoiceRepository->update($invoice, $data, [
            'created_at',
            'due_date',
            'paid_at',
            'tax_rate',
            'note',
            'source_invoice',
            'balance',
            'processed_at'
        ]);
    }
}

<?php

namespace App\Services\Admin\Invoice;

use App\Models\Invoice;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;

class UpdateInvoiceService
{
    private InvoiceRepositoryInterface $invoiceRepository;

    public function __construct(InvoiceRepositoryInterface $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
    }

    public function __invoke(Invoice $invoice, array $data): Invoice
    {
        $attributes = [
            'created_at' => $data['invoice_date'],
            'due_date' => $data['due_date'],
            'paid_at' => $data['paid_at'],
            'tax_rate' => $data['tax_rate'],
        ];

        return $this->invoiceRepository->update($invoice, $attributes, array_keys($attributes));
    }
}

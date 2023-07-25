<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class CalcInvoiceTotalService
{
    private InvoiceRepositoryInterface $invoiceRepository;

    public function __construct(InvoiceRepositoryInterface $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
    }

    public function __invoke(Invoice $invoice): Invoice
    {
        $total = $invoice->sub_total + $invoice->tax;

        return $this->invoiceRepository->update(
            $invoice,
            ['total' => $total,],
            ['total'],
        );
    }
}

<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use App\Repositories\Invoice\Interface\ItemRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class CalcInvoiceTaxService
{
    private InvoiceRepositoryInterface $invoiceRepository;

    public function __construct(InvoiceRepositoryInterface $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
    }

    public function __invoke(Invoice $invoice): Invoice
    {
        $invoice->refresh();
        $tax = ($invoice->sub_total * $invoice->tax_rate) / 100;

        return $this->invoiceRepository->update(
            $invoice,
            ['tax' => $tax,],
            ['tax'],
        );
    }
}

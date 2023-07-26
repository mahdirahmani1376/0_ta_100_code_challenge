<?php

namespace App\Actions\Admin\Invoice;

use App\Models\Invoice;
use App\Services\Admin\Invoice\UpdateInvoiceService;
use App\Services\Invoice\CalcInvoicePriceFieldsService;

class UpdateInvoiceAction
{
    private UpdateInvoiceService $updateInvoiceService;
    private CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService;

    public function __construct(UpdateInvoiceService          $updateInvoiceService,
                                CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService)
    {
        $this->updateInvoiceService = $updateInvoiceService;
        $this->calcInvoicePriceFieldsService = $calcInvoicePriceFieldsService;
    }

    public function __invoke(Invoice $invoice, array $data)
    {
        check_rahkaran($invoice);

        $invoice = ($this->updateInvoiceService)($invoice, $data);

        if (in_array('tax_rate', array_keys($data))) {
            $invoice = ($this->calcInvoicePriceFieldsService)($invoice);
        }

        if (!empty($data['invoice_number'])) {
            // TODO assign invoice number and fiscal_year to this $invoice
        }

        return $invoice;
    }
}

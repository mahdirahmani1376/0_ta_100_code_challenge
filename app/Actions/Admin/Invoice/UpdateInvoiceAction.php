<?php

namespace App\Actions\Admin\Invoice;

use App\Actions\Invoice\CalcInvoicePriceFieldsAction;
use App\Models\Invoice;
use App\Services\Admin\Invoice\UpdateInvoiceService;

class UpdateInvoiceAction
{
    private UpdateInvoiceService $updateInvoiceService;
    private CalcInvoicePriceFieldsAction $calcInvoicePriceFieldsAction;

    public function __construct(UpdateInvoiceService         $updateInvoiceService,
                                CalcInvoicePriceFieldsAction $calcInvoicePriceFieldsAction)
    {
        $this->updateInvoiceService = $updateInvoiceService;
        $this->calcInvoicePriceFieldsAction = $calcInvoicePriceFieldsAction;
    }

    public function __invoke(Invoice $invoice, array $data)
    {
        check_rahkaran($invoice);

        $invoice = ($this->updateInvoiceService)($invoice, $data);

        if (in_array('tax_rate', array_keys($data))) {
            $invoice = ($this->calcInvoicePriceFieldsAction)($invoice);
        }

        if (!empty($data['invoice_number'])) {
            // TODO assign invoice number and fiscal_year to this $invoice
        }

        return $invoice;
    }
}

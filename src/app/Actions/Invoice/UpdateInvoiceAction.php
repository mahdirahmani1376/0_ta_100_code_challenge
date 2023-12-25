<?php

namespace App\Actions\Invoice;

use App\Models\AdminLog;
use App\Models\Invoice;
use App\Services\Invoice\AssignInvoiceNumberService;
use App\Services\Invoice\CalcInvoicePriceFieldsService;
use App\Services\Invoice\UpdateInvoiceService;

class UpdateInvoiceAction
{
    public function __construct(
        private readonly UpdateInvoiceService          $updateInvoiceService,
        private readonly CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService,
        private readonly AssignInvoiceNumberService    $assignInvoiceNumberService,
    )
    {
    }

    public function __invoke(Invoice $invoice, array $data)
    {
        check_rahkaran($invoice);

        $oldState = $invoice->toArray();

        $invoice = ($this->updateInvoiceService)($invoice, $data);

        if (isset($data['tax_rate'])) {
            $invoice = ($this->calcInvoicePriceFieldsService)($invoice);
        }

        if (!empty($data['invoice_number'])) {
            ($this->assignInvoiceNumberService)($invoice, $data['invoice_number'], $data['fiscal_year']);
        }

        admin_log(AdminLog::UPDATE_INVOICE, $invoice, $invoice->getChanges(), $oldState, $data);

        return $invoice;
    }
}

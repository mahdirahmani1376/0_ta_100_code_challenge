<?php

namespace App\Actions\Admin\Invoice;

use App\Models\AdminLog;
use App\Models\Invoice;
use App\Services\Admin\Invoice\AssignInvoiceNumberService;
use App\Services\Admin\Invoice\UpdateInvoiceService;
use App\Services\Invoice\CalcInvoicePriceFieldsService;

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

        if (in_array('tax_rate', array_keys($data))) {
            $invoice = ($this->calcInvoicePriceFieldsService)($invoice);
        }

        if (!empty($data['invoice_number'])) {
            ($this->assignInvoiceNumberService)($invoice, $data['invoice_number']);
        }

        admin_log(AdminLog::UPDATE_INVOICE, $invoice, $invoice->getChanges(), $oldState, $data);

        return $invoice;
    }
}

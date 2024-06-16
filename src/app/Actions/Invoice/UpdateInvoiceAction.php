<?php

namespace App\Actions\Invoice;

use App\Exceptions\SystemException\UpdatingPaidOrRefundedInvoiceNotAllowedException;
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

        if (in_array($invoice->status, [
            Invoice::STATUS_PAID,
            Invoice::STATUS_REFUNDED,
            Invoice::STATUS_COLLECTIONS,
        ])) {
            throw UpdatingPaidOrRefundedInvoiceNotAllowedException::make($invoice->getKey(), $invoice->status);
        }

        $oldState = $invoice->toArray();

        $invoice = ($this->updateInvoiceService)($invoice, $data);

        if (isset($data['tax_rate'])) {
            $invoice = ($this->calcInvoicePriceFieldsService)($invoice);
        }

        if (!empty($data['invoice_number'])) {
            ($this->assignInvoiceNumberService)($invoice, $data['invoice_number'], $data['fiscal_year']);
        }


        return $invoice;
    }
}

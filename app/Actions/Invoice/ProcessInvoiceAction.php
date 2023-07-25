<?php

namespace App\Actions\Invoice;

use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Models\Invoice;
use App\Services\Invoice\CalcInvoiceBalanceService;
use App\Services\Invoice\ChangeInvoiceStatusService;

/**
 * This Action calculates invoice's balance and if the invoice is paid in full
 * and some conditions are met it will try to sync some data from main app like giving the client her product (Hosting,Domain...)
 */
class ProcessInvoiceAction
{
    private ChangeInvoiceStatusService $changeInvoiceStatusService;

    public function __construct(ChangeInvoiceStatusService $changeInvoiceStatusService)
    {
        $this->changeInvoiceStatusService = $changeInvoiceStatusService;
    }

    /**
     * @throws InvoiceLockedAndAlreadyImportedToRahkaranException
     */
    public function __invoke(Invoice $invoice, $shouldProcessPaidInvoice = false): Invoice
    {
        if (
            $shouldProcessPaidInvoice &&
            in_array($invoice->status, [Invoice::STATUS_UNPAID, Invoice::STATUS_COLLECTIONS, Invoice::STATUS_PAYMENT_PENDING]) &&
            $invoice->balance == 0 &&
            $invoice->total > 0
        ) {
            // TODO dispatch paid invoice job
            $invoice = ($this->changeInvoiceStatusService)($invoice, Invoice::STATUS_PAID);
        }

        check_rahkaran($invoice);

        return $invoice;
    }
}

<?php

namespace App\Actions\Invoice;

use App\Models\AdminLog;
use App\Models\Invoice;
use App\Services\Invoice\ChangeInvoiceStatusService;

class ChangeInvoiceStatusAction
{
    public function __construct(
        private readonly ChangeInvoiceStatusService $changeInvoiceStatusService,
        private readonly CancelInvoiceAction        $cancelInvoiceAction,
        private readonly ProcessInvoiceAction       $processInvoiceAction,
    )
    {
    }

    public function __invoke(Invoice $invoice, string $status): Invoice
    {
        check_rahkaran($invoice);

        $oldState = $invoice->toArray();

        if ($status == Invoice::STATUS_CANCELED) {
            $invoice = ($this->cancelInvoiceAction)($invoice);
        } else {
            $invoice = ($this->changeInvoiceStatusService)($invoice, $status);
        }

        if (in_array($status, [Invoice::STATUS_PAID, Invoice::STATUS_COLLECTIONS])) {
            $invoice = ($this->processInvoiceAction)($invoice);
        }

        admin_log(AdminLog::UPDATE_INVOICE_STATUS, $invoice, $invoice->getChanges(), $oldState, ['status' => $status]);

        return $invoice;
    }
}

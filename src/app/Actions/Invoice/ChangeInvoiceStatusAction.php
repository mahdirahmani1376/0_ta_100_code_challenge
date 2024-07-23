<?php

namespace App\Actions\Invoice;

use App\Exceptions\SystemException\UpdateStatusUnacceptableException;
use App\Models\Invoice;
use App\Services\Invoice\ChangeInvoiceStatusService;

class ChangeInvoiceStatusAction
{
    public function __construct(
        private readonly ChangeInvoiceStatusService $changeInvoiceStatusService,
        private readonly CancelInvoiceAction        $cancelInvoiceAction,
        private readonly ProcessInvoiceAction       $processInvoiceAction
    )
    {
    }

    public function __invoke(Invoice $invoice, string $status): Invoice
    {
        check_rahkaran($invoice);

        if (!in_array($status, $invoice->available_status_list)) {
            throw UpdateStatusUnacceptableException::make($status);
        }

        if ($status == Invoice::STATUS_CANCELED) {
            $invoice = ($this->cancelInvoiceAction)($invoice);
        } else {
            $invoice = ($this->changeInvoiceStatusService)($invoice, $status);
        }

        if ($status == Invoice::STATUS_PAID) {
            $invoice = ($this->processInvoiceAction)($invoice);
        }


        return $invoice;
    }
}

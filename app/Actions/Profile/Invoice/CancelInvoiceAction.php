<?php

namespace App\Actions\Profile\Invoice;

use App\Exceptions\SystemException\InvoiceCancellationFailedException;
use App\Models\Invoice;

class CancelInvoiceAction extends \App\Actions\Invoice\CancelInvoiceAction
{
    public function __invoke(Invoice $invoice)
    {
        if ($invoice->status !== Invoice::STATUS_UNPAID) {
            throw InvoiceCancellationFailedException::make($invoice->getKey());
        }

        parent::__invoke($invoice);

        return $invoice;
    }
}

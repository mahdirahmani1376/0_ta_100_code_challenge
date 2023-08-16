<?php

namespace App\Services\Admin\Invoice;

use App\Exceptions\SystemException\AtLeastOneInvoiceItemMustRemainException;
use App\Exceptions\SystemException\InvoiceHasActiveTransactionsException;
use App\Exceptions\SystemException\UpdatingPaidOrRefundedInvoiceNotAllowedException;
use App\Models\Invoice;

class ValidateInvoicesBeforeSplitService
{
    public function __invoke(Invoice $invoice, array $itemIds): void
    {
        if (in_array($invoice->status, [
            Invoice::STATUS_PAID,
            Invoice::STATUS_REFUNDED,
        ])) {
            throw UpdatingPaidOrRefundedInvoiceNotAllowedException::make($invoice->getKey(), $invoice->status);
        }

        // If invoice has at least one successful transaction its balance will be smaller than total
        if ($invoice->total != $invoice->balance) {
            throw InvoiceHasActiveTransactionsException::make($invoice->getKey());
        }

        // If $itemsIds count is the same as $invoice's all items count we cant split it
        if ($invoice->items()->count() == count($itemIds)) {
            throw AtLeastOneInvoiceItemMustRemainException::make($invoice->getKey());
        }
    }
}

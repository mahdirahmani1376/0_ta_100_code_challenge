<?php

namespace App\Actions\Invoice;

use App\Exceptions\SystemException\AtLeastOneInvoiceItemMustRemainException;
use App\Exceptions\SystemException\InvoiceHasActiveTransactionsException;
use App\Exceptions\SystemException\UpdatingPaidOrRefundedInvoiceNotAllowedException;
use App\Models\Invoice;
use App\Services\Invoice\CalcInvoicePriceFieldsService;
use App\Services\Invoice\Item\ReAssignItemToInvoiceService;

class SplitInvoiceAction
{
    public function __construct(
        private readonly ReAssignItemToInvoiceService  $reAssignItemToInvoiceService,
        private readonly StoreInvoiceAction            $storeInvoiceAction,
        private readonly CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService,
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

        // If invoice has at least one successful transaction its balance will be smaller than total
        if ($invoice->total != $invoice->balance) {
            throw InvoiceHasActiveTransactionsException::make($invoice->getKey());
        }

        // If $itemsIds count is the same as $invoice's all items count we cant split it
        if ($invoice->items()->count() == count($data['item_ids'])) {
            throw AtLeastOneInvoiceItemMustRemainException::make($invoice->getKey());
        }

        $invoiceData = [
            'status' => Invoice::STATUS_UNPAID,
            'tax_rate' => $invoice->tax_rate,
            'due_date' => $invoice->due_date,
            'profile_id' => $invoice->profile_id,
            'admin_id' => $data['admin_id'],
        ];
        $newInvoice = ($this->storeInvoiceAction)($invoiceData);

        // Assign items to $newInvoice
        ($this->reAssignItemToInvoiceService)($newInvoice, $data['item_ids']);

        // ReCalc both invoices
        ($this->calcInvoicePriceFieldsService)($invoice);
        $newInvoice = ($this->calcInvoicePriceFieldsService)($newInvoice);


        return $newInvoice;
    }
}

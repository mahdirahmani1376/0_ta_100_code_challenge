<?php

namespace App\Actions\Invoice;

use App\Models\Invoice;
use App\Services\Invoice\IndexItemsByInvoices;
use App\Services\Invoice\ValidateInvoicesBeforeMergeService;

class MergeInvoiceAction
{
    public function __construct(
        private readonly ValidateInvoicesBeforeMergeService $validateInvoicesBeforeMergeService,
        private readonly IndexItemsByInvoices               $indexItemsByInvoices,
        private readonly StoreInvoiceAction                 $storeInvoiceAction,
        private readonly ChangeInvoiceStatusAction          $changeInvoiceStatusAction
    )
    {
    }

    public function __invoke(array $data)
    {
        // Validate if invoices before merging them
        $invoices = ($this->validateInvoicesBeforeMergeService)($data['invoice_ids']);
        // Find all items of invoices
        $items = ($this->indexItemsByInvoices)($invoices);

        $invoiceData = [
            'status' => Invoice::STATUS_UNPAID,
            'tax_rate' => $invoices->first()->tax_rate,
            'due_date' => $invoices->max('due_date'),
            'profile_id' => $invoices->first()->profile_id,
            'admin_id' => data_get($data,'admin_id'),
            'items' => $items->toArray(),
        ];

        $mergedInvoice = ($this->storeInvoiceAction)($invoiceData);

        $invoices->each(function (Invoice $invoice) {
            ($this->changeInvoiceStatusAction)($invoice, Invoice::STATUS_CANCELED);
        });

        return $mergedInvoice;
    }
}

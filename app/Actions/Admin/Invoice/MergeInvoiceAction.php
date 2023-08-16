<?php

namespace App\Actions\Admin\Invoice;

use App\Models\Invoice;
use App\Services\Admin\Invoice\IndexItemsByInvoices;
use App\Services\Admin\Invoice\ValidateInvoicesBeforeMergeService;

class MergeInvoiceAction
{
    private ValidateInvoicesBeforeMergeService $validateInvoicesBeforeMergeService;
    private IndexItemsByInvoices $indexItemsByInvoices;
    private StoreInvoiceAction $storeInvoiceAction;
    private ChangeInvoiceStatusAction $changeInvoiceStatusAction;

    public function __construct(
        ValidateInvoicesBeforeMergeService $validateInvoicesBeforeMergeService,
        IndexItemsByInvoices               $indexItemsByInvoices,
        StoreInvoiceAction                 $storeInvoiceAction,
        ChangeInvoiceStatusAction          $changeInvoiceStatusAction
    )
    {
        $this->validateInvoicesBeforeMergeService = $validateInvoicesBeforeMergeService;
        $this->indexItemsByInvoices = $indexItemsByInvoices;
        $this->storeInvoiceAction = $storeInvoiceAction;
        $this->changeInvoiceStatusAction = $changeInvoiceStatusAction;
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
            'client_id' => $invoices->first()->client_id,
            'admin_id' => $data['admin_id'],
            'items' => $items->toArray(),
        ];

        $mergedInvoice = ($this->storeInvoiceAction)($invoiceData);

        $invoices->each(function (Invoice $invoice) {
            ($this->changeInvoiceStatusAction)($invoice, Invoice::STATUS_CANCELED);
        });

        return $mergedInvoice;
    }
}

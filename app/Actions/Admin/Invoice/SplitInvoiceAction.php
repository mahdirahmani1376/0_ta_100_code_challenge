<?php

namespace App\Actions\Admin\Invoice;

use App\Models\Invoice;
use App\Services\Admin\Invoice\Item\ReAssignItemToInvoiceService;
use App\Services\Admin\Invoice\ValidateInvoicesBeforeSplitService;
use App\Services\Invoice\CalcInvoicePriceFieldsService;

class SplitInvoiceAction
{
    private ValidateInvoicesBeforeSplitService $validateInvoicesBeforeSplitService;
    private ReAssignItemToInvoiceService $reAssignItemToInvoiceService;
    private StoreInvoiceAction $storeInvoiceAction;
    private CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService;

    public function __construct(
        ValidateInvoicesBeforeSplitService $validateInvoicesBeforeSplitService,
        ReAssignItemToInvoiceService       $reAssignItemToInvoiceService,
        StoreInvoiceAction                 $storeInvoiceAction,
        CalcInvoicePriceFieldsService      $calcInvoicePriceFieldsService,
    )
    {
        $this->validateInvoicesBeforeSplitService = $validateInvoicesBeforeSplitService;
        $this->reAssignItemToInvoiceService = $reAssignItemToInvoiceService;
        $this->storeInvoiceAction = $storeInvoiceAction;
        $this->calcInvoicePriceFieldsService = $calcInvoicePriceFieldsService;
    }

    public function __invoke(Invoice $invoice, array $data)
    {
        check_rahkaran($invoice);

        ($this->validateInvoicesBeforeSplitService)($invoice, $data['item_ids']);

        $invoiceData = [
            'status' => Invoice::STATUS_UNPAID,
            'tax_rate' => $invoice->tax_rate,
            'due_date' => $invoice->due_date,
            'client_id' => $invoice->client_id,
            'admin_id' => $data['admin_id'],
        ];
        $newInvoice = ($this->storeInvoiceAction)($invoiceData);

        // Assign items to $newInvoice
        ($this->reAssignItemToInvoiceService)($newInvoice, $data['item_ids']);

        // ReCalc original invoice
        ($this->calcInvoicePriceFieldsService)($invoice);

        return ($this->calcInvoicePriceFieldsService)($newInvoice);
    }
}

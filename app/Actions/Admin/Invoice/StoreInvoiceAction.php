<?php

namespace App\Actions\Admin\Invoice;

use App\Models\Invoice;
use App\Services\Admin\Invoice\Item\StoreItemService;
use App\Services\Admin\Invoice\StoreInvoiceService;
use App\Services\Invoice\CalcInvoicePriceFieldsService;

class StoreInvoiceAction
{
    private StoreInvoiceService $storeInvoiceService;
    private StoreItemService $storeItemService;
    private CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService;
    private ProcessInvoiceAction $processInvoiceAction;

    public function __construct(StoreInvoiceService           $storeInvoiceService,
                                StoreItemService              $storeItemService,
                                CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService,
                                ProcessInvoiceAction          $processInvoiceAction,
    )
    {
        $this->storeInvoiceService = $storeInvoiceService;
        $this->storeItemService = $storeItemService;
        $this->calcInvoicePriceFieldsService = $calcInvoicePriceFieldsService;
        $this->processInvoiceAction = $processInvoiceAction;
    }

    public function __invoke(array $data)
    {
        $invoice = ($this->storeInvoiceService)($data);

        if (!empty($data['items'])) {
            foreach ($data['items'] as $item) {
                ($this->storeItemService)($invoice, $item);
            }
        }

        $invoice = ($this->calcInvoicePriceFieldsService)($invoice);

        if ($invoice->status == Invoice::STATUS_REFUNDED) {
            ($this->processInvoiceAction)($invoice);
        }

        return $invoice;
    }
}

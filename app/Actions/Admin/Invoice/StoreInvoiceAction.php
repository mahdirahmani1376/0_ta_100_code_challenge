<?php

namespace App\Actions\Admin\Invoice;

use App\Services\Admin\Invoice\Item\StoreItemService;
use App\Services\Admin\Invoice\StoreInvoiceService;
use App\Services\Invoice\CalcInvoicePriceFieldsService;

class StoreInvoiceAction
{
    private StoreInvoiceService $storeInvoiceService;
    private StoreItemService $storeItemService;
    private CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService;

    public function __construct(StoreInvoiceService           $storeInvoiceService,
                                StoreItemService              $storeItemService,
                                CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService,
    )
    {
        $this->storeInvoiceService = $storeInvoiceService;
        $this->storeItemService = $storeItemService;
        $this->calcInvoicePriceFieldsService = $calcInvoicePriceFieldsService;
    }

    public function __invoke(array $data)
    {
        $invoice = ($this->storeInvoiceService)($data);

        if (!empty($data['items'])) {
            foreach ($data['items'] as $item) {
                ($this->storeItemService)($invoice, $item);
            }
        }
        // Calculate sub_total, tax, total fields of invoice
        $invoice = ($this->calcInvoicePriceFieldsService)($invoice);
        // TODO check if invoice is paid refunded or ...
//        ($this->processInvoiceAction)($invoice);

        return $invoice;
    }
}

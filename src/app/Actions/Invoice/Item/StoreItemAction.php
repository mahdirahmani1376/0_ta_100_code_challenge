<?php

namespace App\Actions\Invoice\Item;


use App\Models\AdminLog;
use App\Models\Invoice;
use App\Services\Invoice\CalcInvoicePriceFieldsService;
use App\Services\Invoice\Item\StoreItemService;

class StoreItemAction
{
    public function __construct(
        private readonly StoreItemService              $storeItemService,
        private readonly CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService
    )
    {
    }

    public function __invoke(Invoice $invoice, array $data)
    {
        if ($data['amount'] != '0') {
            check_rahkaran($invoice);
        }

        $item = ($this->storeItemService)($invoice, $data);
        if ($data['amount'] != 0) {
            ($this->calcInvoicePriceFieldsService)($invoice);
        }
        admin_log(AdminLog::ADD_INVOICE_ITEM, $item, validatedData: $data);

        return $item;
    }
}

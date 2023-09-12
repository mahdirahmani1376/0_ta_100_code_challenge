<?php

namespace App\Actions\Admin\Invoice\Item;


use App\Models\AdminLog;
use App\Models\Invoice;
use App\Models\Item;
use App\Services\Admin\Invoice\Item\UpdateItemService;
use App\Services\Invoice\CalcInvoicePriceFieldsService;

class UpdateItemAction
{
    public function __construct(
        private readonly UpdateItemService             $updateItemService,
        private readonly CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService
    )
    {
    }

    public function __invoke(Invoice $invoice, Item $item, array $data)
    {
        $oldState = $item->toArray();
        if ($data['amount'] != '0') {
            check_rahkaran($invoice);
        }

        $item = ($this->updateItemService)($item, $data);
        if ($data['amount'] != 0) {
            ($this->calcInvoicePriceFieldsService)($invoice);
        }
        admin_log(AdminLog::EDIT_INVOICE_ITEM, $item, $item->getChanges(), $oldState, $data);

        return $item;
    }
}

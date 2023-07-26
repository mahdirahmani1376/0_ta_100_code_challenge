<?php

namespace App\Actions\Admin\Invoice\Item;


use App\Models\Invoice;
use App\Models\Item;
use App\Services\Admin\Invoice\Item\UpdateItemService;
use App\Services\Invoice\CalcInvoicePriceFieldsService;

class UpdateItemAction
{
    private UpdateItemService $updateItemService;
    private CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService;

    public function __construct(UpdateItemService             $updateItemService,
                                CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService)
    {
        $this->updateItemService = $updateItemService;
        $this->calcInvoicePriceFieldsService = $calcInvoicePriceFieldsService;
    }

    public function __invoke(Invoice $invoice, Item $item, array $data)
    {
        if ($data['amount'] != '0') {
            check_rahkaran($invoice);
        }

        $item = ($this->updateItemService)($item, $data);
        if ($data['amount'] != 0) {
            ($this->calcInvoicePriceFieldsService)($invoice);
        }

        return $item;
    }
}

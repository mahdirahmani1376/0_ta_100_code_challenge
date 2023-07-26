<?php

namespace App\Actions\Admin\Invoice\Item;


use App\Actions\Invoice\CalcInvoicePriceFieldsAction;
use App\Models\Invoice;
use App\Services\Admin\Invoice\Item\StoreItemService;
use App\Services\Invoice\CalcInvoicePriceFieldsService;

class StoreItemAction
{
    private StoreItemService $addItemService;
    private CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService;

    public function __construct(StoreItemService             $addItemService,
                                CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService)
    {
        $this->addItemService = $addItemService;
        $this->calcInvoicePriceFieldsService = $calcInvoicePriceFieldsService;
    }

    public function __invoke(Invoice $invoice, array $data)
    {
        if ($data['amount'] != '0') {
            check_rahkaran($invoice);
        }

        $item = ($this->addItemService)($invoice, $data);
        if ($data['amount'] != 0) {
            ($this->calcInvoicePriceFieldsService)($invoice);
        }

        return $item;
    }
}

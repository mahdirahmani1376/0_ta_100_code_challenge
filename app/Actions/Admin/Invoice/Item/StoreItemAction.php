<?php

namespace App\Actions\Admin\Invoice\Item;


use App\Models\Invoice;
use App\Services\Admin\Invoice\Item\StoreItemService;
use App\Services\Invoice\CalcInvoicePriceFieldsService;

class StoreItemAction
{
    public function __construct(
        private readonly StoreItemService              $addItemService,
        private readonly CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService
    )
    {
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

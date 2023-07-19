<?php

namespace App\Actions\Admin\Invoice\Item;


use App\Actions\Invoice\CalcInvoicePriceFieldsAction;
use App\Exceptions\Http\BadRequestException;
use App\Models\Invoice;
use App\Services\Admin\Invoice\Item\StoreItemService;

class StoreItemAction
{
    private StoreItemService $addItemService;
    private CalcInvoicePriceFieldsAction $calcInvoicePriceFieldsAction;

    public function __construct(StoreItemService             $addItemService,
                                CalcInvoicePriceFieldsAction $calcInvoicePriceFieldsAction)
    {
        $this->addItemService = $addItemService;
        $this->calcInvoicePriceFieldsAction = $calcInvoicePriceFieldsAction;
    }

    public function __invoke(Invoice $invoice, array $data)
    {
        if ($data['amount'] != '0') {
            check_rahkaran($invoice);
        }

        $item = ($this->addItemService)($invoice, $data);
        if ($data['amount'] != 0) {
            ($this->calcInvoicePriceFieldsAction)($invoice);
        }

        return $item;
    }
}

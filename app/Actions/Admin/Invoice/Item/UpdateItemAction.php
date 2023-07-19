<?php

namespace App\Actions\Admin\Invoice\Item;


use App\Actions\Invoice\CalcInvoicePriceFieldsAction;
use App\Exceptions\Http\BadRequestException;
use App\Models\Invoice;
use App\Models\Item;
use App\Services\Admin\Invoice\Item\UpdateItemService;

class UpdateItemAction
{
    private UpdateItemService $updateItemService;
    private CalcInvoicePriceFieldsAction $calcInvoicePriceFieldsAction;

    public function __construct(UpdateItemService            $updateItemService,
                                CalcInvoicePriceFieldsAction $calcInvoicePriceFieldsAction)
    {
        $this->updateItemService = $updateItemService;
        $this->calcInvoicePriceFieldsAction = $calcInvoicePriceFieldsAction;
    }

    public function __invoke(Invoice $invoice, Item $item, array $data)
    {
        if ($data['amount'] != '0') {
            check_rahkaran($invoice);
        }

        $item = ($this->updateItemService)($item, $data);
        if ($data['amount'] != 0) {
            ($this->calcInvoicePriceFieldsAction)($invoice);
        }

        return $item;
    }
}

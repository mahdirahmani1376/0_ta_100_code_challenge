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
        if (($invoice->status == 'Paid' || $invoice->status == 'Refunded' || isset($invoice->rahkaran_id)) && $data['amount'] != '0') {
            throw new BadRequestException('شما نمی‌توانید آیتمی به این فاکتور اضافه کنید');
        }

        $item = ($this->updateItemService)($item, $data);
        if ($data['amount'] != 0) {
            ($this->calcInvoicePriceFieldsAction)($invoice);
        }

        return $item;
    }
}

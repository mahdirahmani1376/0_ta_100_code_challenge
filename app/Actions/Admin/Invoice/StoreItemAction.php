<?php

namespace App\Actions\Admin\Invoice;


use App\Actions\Invoice\CalcInvoicePriceFieldsAction;
use App\Exceptions\Http\BadRequestException;
use App\Models\Invoice;
use App\Services\Admin\Invoice\StoreItemService;

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
        if (($invoice->status == 'Paid' || $invoice->status == 'Refunded' || isset($invoice->rahkaran_id)) && $data['amount'] != '0') {
            throw new BadRequestException('شما نمی‌توانید آیتمی به این فاکتور اضافه کنید');
        }

        $item = ($this->addItemService)($invoice, $data);
        if ($data['amount'] != 0) {
            ($this->calcInvoicePriceFieldsAction)($invoice);
        }

        return $item;
    }
}

<?php

namespace App\Actions\Admin\Invoice;

use App\Models\AdminLog;
use App\Models\Invoice;
use App\Services\Admin\Invoice\Item\StoreItemService;
use App\Services\Admin\Invoice\StoreInvoiceService;
use App\Services\Invoice\CalcInvoicePriceFieldsService;

class StoreInvoiceAction
{

    public function __construct(
        private readonly StoreInvoiceService           $storeInvoiceService,
        private readonly StoreItemService              $storeItemService,
        private readonly CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService,
        private readonly ProcessInvoiceAction          $processInvoiceAction,
    )
    {
    }

    public function __invoke(array $data)
    {
        $invoice = ($this->storeInvoiceService)($data);

        if (!empty($data['items'])) {
            foreach ($data['items'] as $item) {
                ($this->storeItemService)($invoice, $item);
            }
        }

        $invoice = ($this->calcInvoicePriceFieldsService)($invoice);

        if ($invoice->status == Invoice::STATUS_REFUNDED) {
            ($this->processInvoiceAction)($invoice);
        }

        admin_log(AdminLog::CREATE_INVOICE, $invoice, validatedData: $data);

        return $invoice;
    }
}

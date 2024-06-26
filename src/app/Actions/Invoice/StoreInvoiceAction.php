<?php

namespace App\Actions\Invoice;

use App\Events\InvoiceCreated;
use App\Integrations\MainApp\MainAppConfig;
use App\Models\Invoice;
use App\Services\Invoice\CalcInvoicePriceFieldsService;
use App\Services\Invoice\Item\StoreItemService;
use App\Services\Invoice\StoreInvoiceService;

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
        if (!isset($data['tax_rate'])) {
            $data['tax_rate'] = MainAppConfig::get(MainAppConfig::FINANCE_SERVICE_DEFAULT_TAX);
        }

        $invoice = ($this->storeInvoiceService)($data);

        // TODO refactor StoreInvoiceService to take an array or a single item so we have less indents in actions using this service
        if (!empty($data['items'])) {
            foreach ($data['items'] as $item) {
                ($this->storeItemService)($invoice, $item);
            }
        }

        $invoice = ($this->calcInvoicePriceFieldsService)($invoice);

        InvoiceCreated::dispatch($invoice);

        if ($invoice->status == Invoice::STATUS_REFUNDED) {
            ($this->processInvoiceAction)($invoice);
        }


        return $invoice;
    }
}

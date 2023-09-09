<?php

namespace App\Actions\Internal\Domain\Invoice;

use App\Actions\Admin\Invoice\StoreInvoiceAction as AdminStoreInvoiceActionAlias;
use App\Models\Invoice;
use App\Services\Invoice\IndexInvoiceService;

class StoreInvoiceAction
{
    public function __construct(
        private readonly AdminStoreInvoiceActionAlias $storeInvoiceAction,
        private readonly IndexInvoiceService          $indexInvoiceService,
    )
    {
    }

    public function __invoke(array $data): ?Invoice
    {
        // Check if any unpaid invoice exists for any Domains
        // If there was any, exclude that Domain to be added as an Invoice Item
        foreach ($data['items'] as $index => $item) {
            if ((($this->indexInvoiceService)($item))->isNotEmpty()) {
                unset($data['items'][$index]);
            }
        }
        if (count($data['items']) == 0) {
            return null;
        }

        $data['status'] = Invoice::STATUS_UNPAID;
        $data['payment_method'] = Invoice::PAYMENT_METHOD_CREDIT;
        $data['tax_rate'] = Invoice::DEFAULT_TAX_RATE;

        return ($this->storeInvoiceAction)($data);
    }
}

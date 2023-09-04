<?php

namespace App\Actions\Profile\Wallet;

use App\Models\Invoice;
use App\Models\Item;
use App\Services\Invoice\CalcInvoicePriceFieldsService;
use App\Services\Profile\Invoice\Item\StoreItemService;
use App\Services\Profile\Invoice\StoreInvoiceService;

class AddBalanceAction
{
    public function __construct(
        private readonly StoreInvoiceService           $storeInvoiceService,
        private readonly StoreItemService              $storeItemService,
        private readonly CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService
    )
    {
    }

    public function __invoke(array $data)
    {
        // Create an Invoice with one Item which User can pay that invoice later on and "CreditTransaction" and "Transaction" records will be added on to it
        $invoice = ($this->storeInvoiceService)([
            'status' => Invoice::STATUS_UNPAID,
            'client_id' => $data['client_id'],
            'is_credit' => true,
            'description' => __('finance.invoice.ClientCreditInvoiceItem'),
            'tax_rate' => 0,
        ]);
        ($this->storeItemService)($invoice, [
            'amount' => $data['amount'],
            'invoiceable_type' => Item::TYPE_ADD_CLIENT_CREDIT,
        ]);

        return ($this->calcInvoicePriceFieldsService)($invoice);
    }
}

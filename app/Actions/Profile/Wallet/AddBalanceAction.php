<?php

namespace App\Actions\Profile\Wallet;

use App\Models\Invoice;
use App\Models\Item;
use App\Services\Invoice\CalcInvoicePriceFieldsService;
use App\Services\Profile\Invoice\Item\StoreItemService;
use App\Services\Profile\Invoice\StoreInvoiceService;

class AddBalanceAction
{
    private StoreInvoiceService $storeInvoiceService;
    private StoreItemService $storeItemService;
    private CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService;

    public function __construct(
        StoreInvoiceService           $storeInvoiceService,
        StoreItemService              $storeItemService,
        CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService
    )
    {
        $this->storeInvoiceService = $storeInvoiceService;
        $this->storeItemService = $storeItemService;
        $this->calcInvoicePriceFieldsService = $calcInvoicePriceFieldsService;
    }

    public function __invoke(array $data)
    {
        // Create an Invoice with one Item which User can pay that invoice later on and "CreditTransaction" and "Transaction" records will be added on to it
        $invoice = ($this->storeInvoiceService)([
            'status' => Invoice::STATUS_UNPAID,
            'client_id' => $data['client_id'],
            'is_credit' => true,
            'description' => __('finance.invoice.ClientCreditInvoiceItem')
        ]);
        ($this->storeItemService)($invoice, [
            'amount' => $data['amount'],
            'invoiceable_type' => Item::TYPE_ADD_CLIENT_CREDIT,
        ]);

        return ($this->calcInvoicePriceFieldsService)($invoice);
    }
}

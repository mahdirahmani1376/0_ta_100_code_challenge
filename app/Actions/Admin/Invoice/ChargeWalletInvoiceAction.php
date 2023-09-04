<?php

namespace App\Actions\Admin\Invoice;

use App\Models\Invoice;
use App\Models\Item;

class ChargeWalletInvoiceAction
{
    public function __construct(private readonly StoreInvoiceAction $storeInvoiceAction)
    {
    }

    public function __invoke(array $data)
    {
        $invoiceData = [
            'status' => Invoice::STATUS_UNPAID,
            'client_id' => $data['client_id'],
            'tax_rate' => 0,
            'is_credit' => true,
            'admin_id' => $data['admin_id'],
            'items' => [
                [
                    'amount' => $data['amount'],
                    'description' => __('finance.invoice.ClientCreditInvoiceItem'),
                    'invoiceable_type' => Item::TYPE_ADD_CLIENT_CREDIT
                ],
            ]
        ];

        return ($this->storeInvoiceAction)($invoiceData);
    }
}

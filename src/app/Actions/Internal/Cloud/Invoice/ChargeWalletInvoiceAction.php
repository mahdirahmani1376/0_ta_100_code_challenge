<?php

namespace App\Actions\Internal\Cloud\Invoice;

use App\Actions\Admin\Invoice\StoreInvoiceAction;
use App\Models\Invoice;

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
            'items' => [
                [
                    'amount' => $data['item']['amount'],
                    'description' => $data['item']['description'] ?? __('finance.invoice.ClientCreditInvoiceItem'),
                    'invoiceable_type' => $data['item']['invoiceable_type'],
                    'invoiceable_id' => $data['item']['invoiceable_id'],
                ],
            ]
        ];

        return ($this->storeInvoiceAction)($invoiceData);
    }
}

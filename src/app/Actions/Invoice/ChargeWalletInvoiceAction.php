<?php

namespace App\Actions\Invoice;

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
            'status'     => Invoice::STATUS_UNPAID,
            'profile_id' => $data['profile_id'],
            'tax_rate'   => 0,
            'is_credit'  => true,
            'admin_id'   => $data['admin_id'] ?? null,
            'items'      => [
                [
                    'amount'           => $data['amount'],
                    'description'      => data_get($data, 'description', __('finance.invoice.ClientCreditInvoiceItem')),
                    'invoiceable_id'   => data_get($data, 'invoiceable_id'),
                    'invoiceable_type' => data_get($data, 'invoiceable_type', Item::TYPE_ADD_CLIENT_CREDIT),
                ],
            ],
            'due_date'   => data_get($data, 'due_date', now()->endOfDay())
        ];

        $invoice = ($this->storeInvoiceAction)($invoiceData);


        return $invoice;
    }
}

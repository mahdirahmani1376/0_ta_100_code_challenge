<?php

namespace App\Actions\Admin\Invoice;

use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Models\Invoice;

class ChargeWalletInvoiceAction
{
    private StoreInvoiceAction $storeInvoiceAction;

    public function __construct(StoreInvoiceAction $storeInvoiceAction)
    {
        $this->storeInvoiceAction = $storeInvoiceAction;
    }

    public function __invoke(int $clientId, int $amount)
    {
        $data = [
            'status' => Invoice::STATUS_UNPAID,
            'client_id' => $clientId,
            'tax_rate' => 0,
            'items' => [
                [
                    'amount' => $amount,
                    'description' => __('finance.invoice.ClientCreditInvoiceItem'),
                ],
            ]
        ];

        return ($this->storeInvoiceAction)($data);
    }
}

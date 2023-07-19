<?php

namespace App\Actions\Admin\Invoice;

use App\Models\Invoice;

class ChargeWalletInvoiceAction
{
    private StoreInvoiceAction $storeInvoiceAction;

    public function __construct(StoreInvoiceAction $storeInvoiceAction)
    {
        $this->storeInvoiceAction = $storeInvoiceAction;
    }

    /**
     * @throws \Exception
     */
    public function __invoke(int $clientId, int $amount)
    {
        $data = [
            'status' => Invoice::STATUS_UNPAID,
            'client_id' => $clientId,
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

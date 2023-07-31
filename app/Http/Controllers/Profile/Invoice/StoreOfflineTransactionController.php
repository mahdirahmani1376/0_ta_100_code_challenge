<?php

namespace App\Http\Controllers\Profile\Invoice;

use App\Actions\Profile\Invoice\StoreOfflineTransactionAction;
use App\Http\Requests\Profile\Invoice\StoreOfflineTransactionRequest;
use App\Http\Resources\Profile\Invoice\InvoiceResource;
use App\Http\Resources\Profile\OfflineTransaction\OfflineTransactionResource;
use App\Models\Invoice;

class StoreOfflineTransactionController
{
    private StoreOfflineTransactionAction $storeOfflineTransactionAction;

    public function __construct(StoreOfflineTransactionAction $storeOfflineTransactionAction)
    {
        $this->storeOfflineTransactionAction = $storeOfflineTransactionAction;
    }

    /**
     * @param StoreOfflineTransactionRequest $request
     * @param Invoice $invoice
     * @return InvoiceResource
     */
    public function __invoke(StoreOfflineTransactionRequest $request, Invoice $invoice)
    {
        $offlineTransaction = ($this->storeOfflineTransactionAction)($invoice, $request->validated());

        return OfflineTransactionResource::make($offlineTransaction);
    }
}

<?php

namespace App\Http\Controllers\Invoice\OfflineTransaction;

use App\Actions\Invoice\OfflineTransaction\StoreOfflineTransactionAction;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Http\Requests\Invoice\OfflineTransaction\StoreOfflineTransactionRequest;
use App\Http\Resources\Invoice\OfflineTransaction\ShowOfflineTransactionResource;

class StoreOfflineTransactionController
{
    public function __construct(private readonly StoreOfflineTransactionAction $storeOfflineTransactionAction)
    {
    }

    /**
     * @param StoreOfflineTransactionRequest $request
     * @return ShowOfflineTransactionResource
     * @throws InvoiceLockedAndAlreadyImportedToRahkaranException
     */
    public function __invoke(StoreOfflineTransactionRequest $request)
    {
        $offlineTransaction = ($this->storeOfflineTransactionAction)($request->validated());

        return ShowOfflineTransactionResource::make($offlineTransaction);
    }
}

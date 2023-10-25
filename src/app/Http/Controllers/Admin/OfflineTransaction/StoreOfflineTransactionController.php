<?php

namespace App\Http\Controllers\Admin\OfflineTransaction;

use App\Actions\Admin\OfflineTransaction\StoreOfflineTransactionAction;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Http\Requests\Admin\OfflineTransaction\StoreOfflineTransactionRequest;
use App\Http\Resources\Admin\OfflineTransaction\ShowOfflineTransactionResource;

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

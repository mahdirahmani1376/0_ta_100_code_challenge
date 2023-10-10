<?php

namespace App\Http\Controllers\Admin\OfflineTransaction;

use App\Actions\Admin\OfflineTransaction\StoreOfflineTransactionAction;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Http\Requests\Admin\OfflineTransaction\StoreOfflineTransactionRequest;
use App\Http\Resources\Admin\OfflineTransaction\OfflineTransactionResource;
use App\Models\Invoice;

class StoreOfflineTransactionController
{
    public function __construct(private readonly StoreOfflineTransactionAction $storeOfflineTransactionAction)
    {
    }

    /**
     * @param StoreOfflineTransactionRequest $request
     * @return OfflineTransactionResource
     * @throws InvoiceLockedAndAlreadyImportedToRahkaranException
     */
    public function __invoke(StoreOfflineTransactionRequest $request)
    {
        $offlineTransaction = ($this->storeOfflineTransactionAction)($request->validated());

        return OfflineTransactionResource::make($offlineTransaction);
    }
}

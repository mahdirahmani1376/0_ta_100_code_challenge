<?php

namespace App\Http\Controllers\Invoice\OfflineTransaction;

use App\Actions\Invoice\OfflineTransaction\UpdateOfflineTransactionAction;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Http\Requests\Invoice\OfflineTransaction\UpdateOfflineTransactionRequest;
use App\Http\Resources\Invoice\OfflineTransaction\OfflineTransactionResource;
use App\Http\Resources\Invoice\OfflineTransaction\ShowOfflineTransactionResource;
use App\Models\OfflineTransaction;

class UpdateOfflineTransactionController
{
    public function __construct(private readonly UpdateOfflineTransactionAction $updateOfflineTransactionAction)
    {
    }

    /**
     * @param OfflineTransaction $offlineTransaction
     * @param UpdateOfflineTransactionRequest $request
     * @return OfflineTransactionResource
     * @throws InvoiceLockedAndAlreadyImportedToRahkaranException
     */
    public function __invoke(OfflineTransaction $offlineTransaction, UpdateOfflineTransactionRequest $request)
    {
        $offlineTransaction = ($this->updateOfflineTransactionAction)($offlineTransaction, $request->validated());

        return ShowOfflineTransactionResource::make($offlineTransaction);
    }
}

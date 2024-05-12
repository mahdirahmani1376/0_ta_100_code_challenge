<?php

namespace App\Http\Controllers\Invoice\OfflineTransaction;

use App\Actions\Invoice\OfflineTransaction\RejectOfflineTransactionAction;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Http\Requests\Invoice\OfflineTransaction\RejectOfflineTransactionRequest;
use App\Http\Resources\Invoice\OfflineTransaction\OfflineTransactionResource;
use App\Http\Resources\Invoice\OfflineTransaction\ShowOfflineTransactionResource;
use App\Models\OfflineTransaction;

class RejectOfflineTransactionController
{
    public function __construct(private readonly RejectOfflineTransactionAction $rejectOfflineTransactionAction)
    {
    }

    /**
     * @param OfflineTransaction $offlineTransaction
     * @param RejectOfflineTransactionRequest $request
     * @return OfflineTransactionResource
     */
    public function __invoke(OfflineTransaction $offlineTransaction, RejectOfflineTransactionRequest $request)
    {
        ($this->rejectOfflineTransactionAction)($offlineTransaction);

        return ShowOfflineTransactionResource::make($offlineTransaction);
    }
}

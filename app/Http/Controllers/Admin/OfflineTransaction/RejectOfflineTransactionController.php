<?php

namespace App\Http\Controllers\Admin\OfflineTransaction;

use App\Actions\Admin\OfflineTransaction\RejectOfflineTransactionAction;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Http\Requests\Admin\OfflineTransaction\RejectOfflineTransactionRequest;
use App\Http\Resources\Admin\OfflineTransaction\OfflineTransactionResource;
use App\Http\Resources\Admin\OfflineTransaction\ShowOfflineTransactionResource;
use App\Models\OfflineTransaction;

class RejectOfflineTransactionController
{
    public function __construct(private readonly RejectOfflineTransactionAction $rejectOfflineTransactionAction)
    {
    }

    /**
     * @param OfflineTransaction $offlineTransaction
     * @return OfflineTransactionResource
     * @throws InvoiceLockedAndAlreadyImportedToRahkaranException
     */
    public function __invoke(OfflineTransaction $offlineTransaction, RejectOfflineTransactionRequest $request)
    {
        ($this->rejectOfflineTransactionAction)($offlineTransaction);

        return ShowOfflineTransactionResource::make($offlineTransaction);
    }
}

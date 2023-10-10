<?php

namespace App\Http\Controllers\Admin\OfflineTransaction;

use App\Actions\Admin\OfflineTransaction\UpdateOfflineTransactionAction;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Http\Requests\Admin\OfflineTransaction\UpdateOfflineTransactionRequest;
use App\Http\Resources\Admin\OfflineTransaction\OfflineTransactionResource;
use App\Http\Resources\Admin\OfflineTransaction\ShowOfflineTransactionResource;
use App\Models\OfflineTransaction;
use Illuminate\Http\Response;

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

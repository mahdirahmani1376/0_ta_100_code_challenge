<?php

namespace App\Http\Controllers\Admin\OfflineTransaction;

use App\Actions\Admin\OfflineTransaction\StoreOfflineTransactionAction;
use App\Http\Requests\Admin\OfflineTransaction\StoreOfflineTransactionRequest;
use App\Http\Resources\Admin\OfflineTransaction\OfflineTransactionResource;
use App\Models\Invoice;

class StoreOfflineTransactionController
{
    private StoreOfflineTransactionAction $storeOfflineTransactionAction;

    public function __construct(StoreOfflineTransactionAction $storeOfflineTransactionAction)
    {
        $this->storeOfflineTransactionAction = $storeOfflineTransactionAction;
    }

    /**
     * @throws \Exception
     */
    public function __invoke(StoreOfflineTransactionRequest $request)
    {
        $offlineTransaction = ($this->storeOfflineTransactionAction)($request->validated());

        return OfflineTransactionResource::make($offlineTransaction);
    }
}

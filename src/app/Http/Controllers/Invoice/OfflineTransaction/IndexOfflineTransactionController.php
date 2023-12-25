<?php

namespace App\Http\Controllers\Invoice\OfflineTransaction;

use App\Actions\Invoice\OfflineTransaction\IndexOfflineTransactionAction;
use App\Http\Requests\Invoice\OfflineTransaction\IndexOfflineTransactionRequest;
use App\Http\Resources\Invoice\OfflineTransaction\OfflineTransactionResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexOfflineTransactionController
{
    public function __construct(private readonly IndexOfflineTransactionAction $indexOfflineTransactionAction)
    {
    }

    /**
     * @param IndexOfflineTransactionRequest $request
     * @return AnonymousResourceCollection
     */
    public function __invoke(IndexOfflineTransactionRequest $request)
    {
        $offlineTransactions = ($this->indexOfflineTransactionAction)($request->validated());

        return OfflineTransactionResource::collection($offlineTransactions);
    }
}

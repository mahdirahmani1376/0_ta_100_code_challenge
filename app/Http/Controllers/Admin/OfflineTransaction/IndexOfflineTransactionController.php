<?php

namespace App\Http\Controllers\Admin\OfflineTransaction;

use App\Actions\Admin\OfflineTransaction\IndexOfflineTransactionAction;
use App\Http\Requests\Admin\OfflineTransaction\IndexOfflineTransactionRequest;
use App\Http\Resources\Admin\OfflineTransaction\OfflineTransactionResource;
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

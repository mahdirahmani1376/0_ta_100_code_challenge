<?php

namespace App\Http\Controllers\Admin\OfflineTransaction;

use App\Actions\Admin\OfflineTransaction\IndexSimilarOfflineTransactionAction;
use App\Http\Resources\Admin\OfflineTransaction\ShowOfflineTransactionResource;
use App\Models\OfflineTransaction;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexSimilarOfflineTransactionController
{
    public function __construct(private readonly IndexSimilarOfflineTransactionAction $indexSimilarOfflineTransactionAction)
    {
    }

    /**
     * @param OfflineTransaction $offlineTransaction
     * @return AnonymousResourceCollection
     */
    public function __invoke(OfflineTransaction $offlineTransaction)
    {
        $offlineTransactions = ($this->indexSimilarOfflineTransactionAction)($offlineTransaction);

        return ShowOfflineTransactionResource::collection($offlineTransactions);
    }
}

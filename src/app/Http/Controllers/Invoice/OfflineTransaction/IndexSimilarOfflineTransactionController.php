<?php

namespace App\Http\Controllers\Invoice\OfflineTransaction;

use App\Actions\Invoice\OfflineTransaction\IndexSimilarOfflineTransactionAction;
use App\Http\Resources\Invoice\OfflineTransaction\ShowOfflineTransactionResource;
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

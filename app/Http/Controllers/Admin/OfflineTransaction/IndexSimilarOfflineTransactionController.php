<?php

namespace App\Http\Controllers\Admin\OfflineTransaction;

use App\Actions\Admin\OfflineTransaction\IndexSimilarOfflineTransactionAction;
use App\Http\Resources\Admin\OfflineTransaction\ShowOfflineTransactionResource;
use App\Models\OfflineTransaction;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexSimilarOfflineTransactionController
{
    private IndexSimilarOfflineTransactionAction $indexSimilarOfflineTransactionAction;

    public function __construct(IndexSimilarOfflineTransactionAction $indexSimilarOfflineTransactionAction)
    {
        $this->indexSimilarOfflineTransactionAction = $indexSimilarOfflineTransactionAction;
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

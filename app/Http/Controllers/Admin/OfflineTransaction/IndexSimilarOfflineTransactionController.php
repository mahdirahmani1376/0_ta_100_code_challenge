<?php

namespace App\Http\Controllers\Admin\OfflineTransaction;

use App\Actions\Admin\OfflineTransaction\IndexOfflineTransactionAction;
use App\Actions\Admin\OfflineTransaction\IndexSimilarOfflineTransactionAction;
use App\Http\Requests\Admin\OfflineTransaction\IndexOfflineTransactionRequest;
use App\Http\Resources\Admin\OfflineTransaction\OfflineTransactionResource;
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

        return OfflineTransactionResource::collection($offlineTransactions);
    }
}

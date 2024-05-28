<?php

namespace App\Http\Controllers\Invoice\OfflineTransaction;

use App\Actions\Invoice\OfflineTransaction\IndexSimilarOfflineTransactionAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Invoice\OfflineTransaction\ShowOfflineTransactionResource;
use App\Models\OfflineTransaction;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexSimilarOfflineTransactionController extends Controller
{
    public function __construct(private readonly IndexSimilarOfflineTransactionAction $indexSimilarOfflineTransactionAction)
    {
        parent::__construct();
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

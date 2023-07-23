<?php

namespace App\Actions\Admin\OfflineTransaction;

use App\Models\OfflineTransaction;
use App\Services\Admin\OfflineTransaction\IndexSimilarOfflineTransactionService;

class IndexSimilarOfflineTransactionAction
{
    private IndexSimilarOfflineTransactionService $indexSimilarOfflineTransactionService;

    public function __construct(IndexSimilarOfflineTransactionService $indexSimilarOfflineTransactionService)
    {
        $this->indexSimilarOfflineTransactionService = $indexSimilarOfflineTransactionService;
    }

    public function __invoke(OfflineTransaction $offlineTransaction)
    {
        return ($this->indexSimilarOfflineTransactionService)($offlineTransaction);
    }
}

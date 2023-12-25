<?php

namespace App\Actions\Invoice\OfflineTransaction;

use App\Models\OfflineTransaction;
use App\Services\Invoice\OfflineTransaction\IndexSimilarOfflineTransactionService;

class IndexSimilarOfflineTransactionAction
{
    public function __construct(private readonly IndexSimilarOfflineTransactionService $indexSimilarOfflineTransactionService)
    {
    }

    public function __invoke(OfflineTransaction $offlineTransaction)
    {
        return ($this->indexSimilarOfflineTransactionService)($offlineTransaction);
    }
}

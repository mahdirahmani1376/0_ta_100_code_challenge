<?php

namespace App\Actions\Admin\OfflineTransaction;

use App\Models\OfflineTransaction;
use App\Services\Admin\OfflineTransaction\IndexSimilarOfflineTransactionService;

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

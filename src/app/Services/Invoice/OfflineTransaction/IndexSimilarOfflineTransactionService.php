<?php

namespace App\Services\Invoice\OfflineTransaction;

use App\Models\OfflineTransaction;
use App\Repositories\OfflineTransaction\Interface\OfflineTransactionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IndexSimilarOfflineTransactionService
{
    public function __construct(private readonly OfflineTransactionRepositoryInterface $offlineTransactionRepository)
    {
    }

    public function __invoke(OfflineTransaction $offlineTransaction): LengthAwarePaginator
    {
        return $this->offlineTransactionRepository->adminIndexSimilar($offlineTransaction);
    }
}

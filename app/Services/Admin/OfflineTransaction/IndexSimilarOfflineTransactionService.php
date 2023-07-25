<?php

namespace App\Services\Admin\OfflineTransaction;

use App\Models\OfflineTransaction;
use App\Repositories\OfflineTransaction\Interface\OfflineTransactionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IndexSimilarOfflineTransactionService
{
    private OfflineTransactionRepositoryInterface $offlineTransactionRepository;

    public function __construct(OfflineTransactionRepositoryInterface $offlineTransactionRepository)
    {
        $this->offlineTransactionRepository = $offlineTransactionRepository;
    }

    public function __invoke(OfflineTransaction $offlineTransaction): LengthAwarePaginator
    {
        return $this->offlineTransactionRepository->adminIndexSimilar($offlineTransaction);
    }
}

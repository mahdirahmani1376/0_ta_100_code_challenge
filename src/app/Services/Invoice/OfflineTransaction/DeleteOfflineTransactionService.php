<?php

namespace App\Services\Invoice\OfflineTransaction;

use App\Models\OfflineTransaction;
use App\Repositories\OfflineTransaction\Interface\OfflineTransactionRepositoryInterface;

class DeleteOfflineTransactionService
{
    private OfflineTransactionRepositoryInterface $offlineTransactionRepository;

    public function __construct(OfflineTransactionRepositoryInterface $offlineTransactionRepository)
    {
        $this->offlineTransactionRepository = $offlineTransactionRepository;
    }

    public function __invoke(OfflineTransaction $offlineTransaction)
    {
        return $this->offlineTransactionRepository->delete($offlineTransaction);
    }
}

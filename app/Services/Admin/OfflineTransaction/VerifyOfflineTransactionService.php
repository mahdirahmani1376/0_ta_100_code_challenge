<?php

namespace App\Services\Admin\OfflineTransaction;

use App\Models\OfflineTransaction;
use App\Repositories\OfflineTransaction\Interface\OfflineTransactionRepositoryInterface;

class VerifyOfflineTransactionService
{
    private OfflineTransactionRepositoryInterface $offlineTransactionRepository;

    public function __construct(OfflineTransactionRepositoryInterface $offlineTransactionRepository)
    {
        $this->offlineTransactionRepository = $offlineTransactionRepository;
    }

    public function __invoke(OfflineTransaction $offlineTransaction)
    {
        return $this->offlineTransactionRepository->update(
            $offlineTransaction,
            ['status' => OfflineTransaction::STATUS_CONFIRMED,],
            ['status',]
        );
    }
}

<?php

namespace App\Services\Admin\OfflineTransaction;

use App\Models\OfflineTransaction;
use App\Repositories\OfflineTransaction\Interface\OfflineTransactionRepositoryInterface;

class VerifyOfflineTransactionService
{
    public function __construct(private readonly OfflineTransactionRepositoryInterface $offlineTransactionRepository)
    {
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

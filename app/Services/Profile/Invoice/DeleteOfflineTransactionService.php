<?php

namespace App\Services\Profile\Invoice;

use App\Models\OfflineTransaction;
use App\Repositories\OfflineTransaction\Interface\OfflineTransactionRepositoryInterface;

class DeleteOfflineTransactionService
{
    public function __construct(private readonly OfflineTransactionRepositoryInterface $offlineTransactionRepository)
    {
    }

    public function __invoke(OfflineTransaction $offlineTransaction)
    {
        return $this->offlineTransactionRepository->delete($offlineTransaction);
    }
}

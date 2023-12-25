<?php

namespace App\Services\Invoice\OfflineTransaction;

use App\Models\OfflineTransaction;
use App\Repositories\OfflineTransaction\Interface\OfflineTransactionRepositoryInterface;

class UpdateOfflineTransactionService
{
    public function __construct(private readonly OfflineTransactionRepositoryInterface $offlineTransactionRepository)
    {
    }

    public function __invoke(OfflineTransaction $offlineTransaction, array $data): OfflineTransaction
    {
        return $this->offlineTransactionRepository->update($offlineTransaction, $data, [
            'paid_at',
            'bank_account_id',
            'admin_id',
            'payment_method',
            'tracking_code',
            'mobile',
            'description',
        ]);
    }
}

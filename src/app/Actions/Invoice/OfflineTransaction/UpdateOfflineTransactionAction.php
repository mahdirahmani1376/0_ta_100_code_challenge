<?php

namespace App\Actions\Invoice\OfflineTransaction;

use App\Models\AdminLog;
use App\Models\OfflineTransaction;
use App\Services\Invoice\OfflineTransaction\UpdateOfflineTransactionService;
use App\Services\Invoice\Transaction\UpdateTransactionService;

class UpdateOfflineTransactionAction
{
    public function __construct(
        private readonly UpdateOfflineTransactionService $updateOfflineTransactionService,
        private readonly UpdateTransactionService        $updateTransactionService,
    )
    {
    }

    public function __invoke(OfflineTransaction $offlineTransaction, array $data)
    {
        check_rahkaran($offlineTransaction->invoice);

        $oldState = $offlineTransaction->toArray();
        $offlineTransaction = ($this->updateOfflineTransactionService)($offlineTransaction, $data);

        $transactionData = [
            'created_at' => $data['paid_at'],
            'tracking_code' => $data['tracking_code']
        ];
        ($this->updateTransactionService)($offlineTransaction->transaction, $transactionData);

        admin_log(AdminLog::UPDATE_OFFLINE_TRANSACTION, $offlineTransaction, $offlineTransaction->getChanges(), $oldState, $data);

        return $offlineTransaction;
    }
}

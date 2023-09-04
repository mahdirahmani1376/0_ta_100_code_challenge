<?php

namespace App\Actions\Admin\OfflineTransaction;

use App\Models\OfflineTransaction;
use App\Services\Admin\OfflineTransaction\UpdateOfflineTransactionService;
use App\Services\Admin\Transaction\UpdateTransactionService;
use App\Services\Transaction\FindTransactionByTrackingCodeService;

class UpdateOfflineTransactionAction
{
    public function __construct(
        private readonly UpdateOfflineTransactionService      $updateOfflineTransactionService,
        private readonly UpdateTransactionService             $updateTransactionService,
        private readonly FindTransactionByTrackingCodeService $findTransactionByTrackingCodeService
    )
    {
    }

    public function __invoke(OfflineTransaction $offlineTransaction, array $data)
    {
        check_rahkaran($offlineTransaction->invoice);

        $transaction = ($this->findTransactionByTrackingCodeService)($offlineTransaction->tracking_code);

        $offlineTransaction = ($this->updateOfflineTransactionService)($offlineTransaction, $data);

        $transactionData = [
            'created_at' => $data['paid_at'],
            'tracking_code' => $data['tracking_code']
        ];
        ($this->updateTransactionService)($transaction, $transactionData);

        return $offlineTransaction;
    }
}

<?php

namespace App\Actions\Admin\OfflineTransaction;

use App\Models\OfflineTransaction;
use App\Services\Admin\OfflineTransaction\RejectOfflineTransactionService;
use App\Services\Admin\Transaction\RejectTransactionService;
use App\Services\Transaction\FindTransactionByTrackingCodeService;

class RejectOfflineTransactionAction
{
    public function __construct(
        private readonly FindTransactionByTrackingCodeService $findTransactionByTrackingCodeService,
        private readonly RejectOfflineTransactionService      $rejectOfflineTransactionService,
        private readonly RejectTransactionService             $rejectTransactionService,
    )
    {
    }

    public function __invoke(OfflineTransaction $offlineTransaction): OfflineTransaction
    {
        check_rahkaran($offlineTransaction->invoice);

        ($this->rejectOfflineTransactionService)($offlineTransaction);
        $transaction = ($this->findTransactionByTrackingCodeService)($offlineTransaction->tracking_code);
        ($this->rejectTransactionService)($transaction);

        return $offlineTransaction;
    }
}

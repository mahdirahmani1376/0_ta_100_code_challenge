<?php

namespace App\Actions\Admin\OfflineTransaction;

use App\Models\OfflineTransaction;
use App\Services\Admin\OfflineTransaction\RejectOfflineTransactionService;
use App\Services\Admin\Transaction\RejectTransactionService;
use App\Services\Transaction\FindTransactionByTrackingCodeService;

class RejectOfflineTransactionAction
{
    private FindTransactionByTrackingCodeService $findTransactionByTrackingCodeService;
    private RejectOfflineTransactionService $rejectOfflineTransactionService;
    private RejectTransactionService $rejectTransactionService;

    public function __construct(
        FindTransactionByTrackingCodeService $findTransactionByTrackingCodeService,
        RejectOfflineTransactionService      $rejectOfflineTransactionService,
        RejectTransactionService             $rejectTransactionService,
    )
    {
        $this->findTransactionByTrackingCodeService = $findTransactionByTrackingCodeService;
        $this->rejectOfflineTransactionService = $rejectOfflineTransactionService;
        $this->rejectTransactionService = $rejectTransactionService;
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

<?php

namespace App\Actions\Invoice\OfflineTransaction;

use App\Exceptions\SystemException\NotAuthorizedException;
use App\Models\OfflineTransaction;
use App\Services\Invoice\OfflineTransaction\RejectOfflineTransactionService;
use App\Services\Invoice\Transaction\RejectTransactionService;

class RejectOfflineTransactionAction
{
    public function __construct(
        private readonly RejectOfflineTransactionService $rejectOfflineTransactionService,
        private readonly RejectTransactionService        $rejectTransactionService,
    )
    {
    }

    public function __invoke(OfflineTransaction $offlineTransaction): OfflineTransaction
    {
        check_rahkaran($offlineTransaction->invoice);

        if ($offlineTransaction->status != OfflineTransaction::STATUS_PENDING) {
            throw NotAuthorizedException::make();
        }

        $oldState = $offlineTransaction->toArray();
        ($this->rejectOfflineTransactionService)($offlineTransaction);
        ($this->rejectTransactionService)($offlineTransaction->transaction);


        return $offlineTransaction;
    }
}

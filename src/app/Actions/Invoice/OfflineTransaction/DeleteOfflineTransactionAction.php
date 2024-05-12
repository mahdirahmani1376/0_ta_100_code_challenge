<?php

namespace App\Actions\Invoice\OfflineTransaction;

use App\Exceptions\Http\BadRequestException;
use App\Models\AdminLog;
use App\Models\OfflineTransaction;
use App\Services\Invoice\OfflineTransaction\DeleteOfflineTransactionService;
use App\Services\Invoice\Transaction\RejectTransactionService;

class DeleteOfflineTransactionAction
{
    public function __construct(
        private readonly DeleteOfflineTransactionService $offlineTransactionService,
        private readonly RejectTransactionService        $rejectTransactionService,
    )
    {
    }

    public function __invoke(OfflineTransaction $offlineTransaction, array $data)
    {
        if ($offlineTransaction->status != OfflineTransaction::STATUS_PENDING) {
            throw new BadRequestException(__('finance.error.OnlyPendingOfflinePaymentAllowed'));
        }
        if (isset($data['profile_id']) && $data['profile_id'] != $offlineTransaction->profile_id) {
            throw new BadRequestException(__('finance.invoice.AccessDeniedToOfflineTransaction'));
        }

        ($this->rejectTransactionService)($offlineTransaction->transaction);

        admin_log(AdminLog::DELETE_OFFLINE_TRANSACTION, $offlineTransaction);

        return ($this->offlineTransactionService)($offlineTransaction);
    }
}

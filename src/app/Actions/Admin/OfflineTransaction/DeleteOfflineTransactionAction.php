<?php

namespace App\Actions\Admin\OfflineTransaction;

use App\Models\AdminLog;
use App\Models\OfflineTransaction;
use App\Services\Admin\OfflineTransaction\DeleteOfflineTransactionService;

class DeleteOfflineTransactionAction
{
    public function __construct(private readonly DeleteOfflineTransactionService $offlineTransactionService)
    {
    }

    public function __invoke(OfflineTransaction $offlineTransaction)
    {
        admin_log(AdminLog::DELETE_OFFLINE_TRANSACTION, $offlineTransaction);

        return ($this->offlineTransactionService)($offlineTransaction);
    }
}

<?php

namespace App\Actions\Admin\OfflineTransaction;

use App\Models\OfflineTransaction;
use App\Services\Admin\OfflineTransaction\DeleteOfflineTransactionService;

class DeleteOfflineTransactionAction
{
    private DeleteOfflineTransactionService $offlineTransactionService;

    public function __construct(DeleteOfflineTransactionService $offlineTransactionService)
    {
        $this->offlineTransactionService = $offlineTransactionService;
    }

    public function __invoke(OfflineTransaction $offlineTransaction)
    {
        return ($this->offlineTransactionService)($offlineTransaction);
    }
}

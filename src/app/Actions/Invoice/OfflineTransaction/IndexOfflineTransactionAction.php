<?php

namespace App\Actions\Invoice\OfflineTransaction;

use App\Services\Invoice\OfflineTransaction\IndexOfflineTransactionService;

class IndexOfflineTransactionAction
{
    public function __construct(private readonly IndexOfflineTransactionService $offlineTransactionService)
    {
    }

    public function __invoke(array $data)
    {
        return ($this->offlineTransactionService)($data);
    }
}

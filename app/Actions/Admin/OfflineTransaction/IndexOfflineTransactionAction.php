<?php

namespace App\Actions\Admin\OfflineTransaction;

use App\Services\Admin\OfflineTransaction\IndexOfflineTransactionService;

class IndexOfflineTransactionAction
{
    private IndexOfflineTransactionService $offlineTransactionService;

    public function __construct(IndexOfflineTransactionService $offlineTransactionService)
    {
        $this->offlineTransactionService = $offlineTransactionService;
    }

    public function __invoke(array $data)
    {
        return ($this->offlineTransactionService)($data);
    }
}

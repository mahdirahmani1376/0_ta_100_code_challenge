<?php

namespace App\Actions\Admin\OfflineTransaction;

use App\Services\Admin\OfflineTransaction\IndexOfflineTransactionService;

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

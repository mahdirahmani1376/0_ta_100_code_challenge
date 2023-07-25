<?php

namespace App\Actions\Admin\Invoice\Transaction;

use App\Services\Admin\Transaction\IndexTransactionService;

class IndexTransactionAction
{
    private IndexTransactionService $indexTransactionService;

    public function __construct(IndexTransactionService $indexTransactionService)
    {
        $this->indexTransactionService = $indexTransactionService;
    }

    public function __invoke(array $data)
    {
        return ($this->indexTransactionService)($data);
    }
}

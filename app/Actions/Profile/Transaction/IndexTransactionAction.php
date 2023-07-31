<?php

namespace App\Actions\Profile\Transaction;

use App\Services\Profile\Transaction\IndexTransactionService;

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

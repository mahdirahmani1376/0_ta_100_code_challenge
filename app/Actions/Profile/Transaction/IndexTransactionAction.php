<?php

namespace App\Actions\Profile\Transaction;

use App\Services\Profile\Transaction\IndexTransactionService;

class IndexTransactionAction
{
    public function __construct(private readonly IndexTransactionService $indexTransactionService)
    {
    }

    public function __invoke(array $data)
    {
        return ($this->indexTransactionService)($data);
    }
}

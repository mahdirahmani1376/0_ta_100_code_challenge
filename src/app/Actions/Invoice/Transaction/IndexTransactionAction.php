<?php

namespace App\Actions\Invoice\Transaction;

use App\Services\Invoice\Transaction\IndexTransactionService;

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

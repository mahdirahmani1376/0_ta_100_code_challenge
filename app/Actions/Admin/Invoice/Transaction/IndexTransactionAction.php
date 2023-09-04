<?php

namespace App\Actions\Admin\Invoice\Transaction;

use App\Services\Admin\Transaction\IndexTransactionService;

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

<?php

namespace App\Actions\Admin\Wallet;

use App\Services\Admin\Wallet\IndexCreditTransactionService;

class IndexCreditTransactionAction
{
    public function __construct(private readonly IndexCreditTransactionService $indexCreditTransactionService)
    {
    }

    public function __invoke(array $data)
    {
        return ($this->indexCreditTransactionService)($data);
    }
}

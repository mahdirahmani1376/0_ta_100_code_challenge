<?php

namespace App\Actions\Wallet\CreditTransaction;

use App\Services\Wallet\IndexCreditTransactionService;

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

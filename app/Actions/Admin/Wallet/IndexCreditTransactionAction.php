<?php

namespace App\Actions\Admin\Wallet;

use App\Services\Admin\Wallet\IndexCreditTransactionService;

class IndexCreditTransactionAction
{
    private IndexCreditTransactionService $indexCreditTransactionService;

    public function __construct(IndexCreditTransactionService $indexCreditTransactionService)
    {
        $this->indexCreditTransactionService = $indexCreditTransactionService;
    }

    public function __invoke(array $data)
    {
        return ($this->indexCreditTransactionService)($data);
    }
}

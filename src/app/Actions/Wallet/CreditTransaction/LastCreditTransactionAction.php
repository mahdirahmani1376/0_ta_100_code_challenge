<?php

namespace App\Actions\Wallet\CreditTransaction;

use App\Services\Wallet\LastCreditTransactionService;

class LastCreditTransactionAction
{
    public function __construct(private readonly LastCreditTransactionService $lastCreditTransactionService)
    {
    }

    public function __invoke(array $data)
    {
        return ($this->lastCreditTransactionService)($data);
    }
}

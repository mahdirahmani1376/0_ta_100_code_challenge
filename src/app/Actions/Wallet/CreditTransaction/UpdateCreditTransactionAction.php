<?php

namespace App\Actions\Wallet\CreditTransaction;

use App\Models\CreditTransaction;
use App\Services\Wallet\UpdateCreditTransactionService;

class UpdateCreditTransactionAction
{
    public function __construct(
        private readonly UpdateCreditTransactionService $updateCreditTransactionService
    )
    {
    }

    public function __invoke(CreditTransaction $creditTransaction, array $data): CreditTransaction
    {
        return ($this->updateCreditTransactionService)($creditTransaction, $data);
    }
}

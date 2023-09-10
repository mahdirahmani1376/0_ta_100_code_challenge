<?php

namespace App\Services\Wallet;

use App\Models\CreditTransaction;
use App\Repositories\Wallet\Interface\CreditTransactionRepositoryInterface;

class UpdateCreditTransactionService
{
    public function __construct(private readonly CreditTransactionRepositoryInterface $creditTransactionRepository)
    {
    }

    public function __invoke(CreditTransaction $creditTransaction, array $data): CreditTransaction
    {
        return $this->creditTransactionRepository->update($creditTransaction, $data, [
            'invoice_id',
            'description',
        ]);
    }
}

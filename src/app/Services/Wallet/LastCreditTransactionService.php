<?php

namespace App\Services\Wallet;

use App\Repositories\Wallet\Interface\CreditTransactionRepositoryInterface;

class LastCreditTransactionService
{
    public function __construct(private readonly CreditTransactionRepositoryInterface $creditTransactionRepository)
    {
    }

    public function __invoke(array $data): int
    {
        return $this->creditTransactionRepository->lastCreditTransaction($data['profile_id'], $data['credit_transaction_id']);
    }
}

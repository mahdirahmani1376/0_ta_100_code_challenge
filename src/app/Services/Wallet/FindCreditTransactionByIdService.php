<?php

namespace App\Services\Wallet;

use App\Models\CreditTransaction;
use App\Repositories\Wallet\Interface\CreditTransactionRepositoryInterface;

class FindCreditTransactionByIdService
{
    public function __construct(private readonly CreditTransactionRepositoryInterface $creditTransactionRepository)
    {
    }

    public function __invoke(int $id): ?CreditTransaction
    {
        return $this->creditTransactionRepository->find($id);
    }
}

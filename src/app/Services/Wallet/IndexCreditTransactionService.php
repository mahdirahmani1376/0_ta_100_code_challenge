<?php

namespace App\Services\Wallet;

use App\Repositories\Wallet\Interface\CreditTransactionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IndexCreditTransactionService
{
    public function __construct(private readonly CreditTransactionRepositoryInterface $creditTransactionRepository)
    {
    }

    public function __invoke(int $profileId): LengthAwarePaginator
    {
        return $this->creditTransactionRepository->indexByProfileId($profileId);
    }
}

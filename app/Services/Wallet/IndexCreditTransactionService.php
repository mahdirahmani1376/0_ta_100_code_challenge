<?php

namespace App\Services\Wallet;

use App\Repositories\Wallet\Interface\CreditTransactionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IndexCreditTransactionService
{
    private CreditTransactionRepositoryInterface $creditTransactionRepository;

    public function __construct(CreditTransactionRepositoryInterface $creditTransactionRepository)
    {
        $this->creditTransactionRepository = $creditTransactionRepository;
    }

    public function __invoke(int $clientId): LengthAwarePaginator
    {
        return $this->creditTransactionRepository->indexByClientId($clientId);
    }
}

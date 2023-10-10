<?php

namespace App\Services\Admin\Wallet;

use App\Repositories\Wallet\Interface\CreditTransactionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IndexCreditTransactionService
{
    public function __construct(private readonly CreditTransactionRepositoryInterface $creditTransactionRepository)
    {
    }

    public function __invoke(array $data): LengthAwarePaginator
    {
        return $this->creditTransactionRepository->adminIndex($data);
    }
}

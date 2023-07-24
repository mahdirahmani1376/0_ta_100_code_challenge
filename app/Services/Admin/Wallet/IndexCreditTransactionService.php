<?php

namespace App\Services\Admin\Wallet;

use App\Repositories\Wallet\Interface\CreditTransactionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IndexCreditTransactionService
{
    private CreditTransactionRepositoryInterface $creditTransactionRepository;

    public function __construct(CreditTransactionRepositoryInterface $creditTransactionRepository)
    {
        $this->creditTransactionRepository = $creditTransactionRepository;
    }

    public function __invoke(array $data): LengthAwarePaginator
    {
        return $this->creditTransactionRepository->adminIndex($data);
    }
}

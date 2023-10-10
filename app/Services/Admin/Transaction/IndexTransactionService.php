<?php

namespace App\Services\Admin\Transaction;

use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class IndexTransactionService
{
    public function __construct(private readonly TransactionRepositoryInterface $transactionRepository)
    {
    }

    public function __invoke(array $data): LengthAwarePaginator|Collection
    {
        return $this->transactionRepository->adminIndex($data);
    }
}

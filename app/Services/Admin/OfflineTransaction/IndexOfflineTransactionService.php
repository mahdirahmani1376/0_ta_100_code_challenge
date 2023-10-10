<?php

namespace App\Services\Admin\OfflineTransaction;

use App\Repositories\OfflineTransaction\Interface\OfflineTransactionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IndexOfflineTransactionService
{
    public function __construct(private readonly OfflineTransactionRepositoryInterface $offlineTransactionRepository)
    {
    }

    public function __invoke(array $data): LengthAwarePaginator
    {
        return $this->offlineTransactionRepository->adminIndex($data);
    }
}

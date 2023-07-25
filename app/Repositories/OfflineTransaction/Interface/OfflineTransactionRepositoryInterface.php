<?php

namespace App\Repositories\OfflineTransaction\Interface;

use App\Models\OfflineTransaction;
use App\Repositories\Base\Interface\EloquentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OfflineTransactionRepositoryInterface extends EloquentRepositoryInterface
{
    public function adminIndex(array $data): LengthAwarePaginator;
    public function adminIndexSimilar(OfflineTransaction $offlineTransaction): LengthAwarePaginator;
}

<?php

namespace App\Repositories\Wallet\Interface;

use App\Repositories\Base\Interface\EloquentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CreditTransactionRepositoryInterface extends EloquentRepositoryInterface
{
    public function indexByClientId(int $clientId): LengthAwarePaginator;

    public function sum(int $clientId): int;
}

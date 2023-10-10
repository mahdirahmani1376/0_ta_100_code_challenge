<?php

namespace App\Repositories\ClientCashout\Interface;

use App\Repositories\Base\Interface\EloquentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ClientCashoutRepositoryInterface extends EloquentRepositoryInterface
{
    public function adminIndex(array $data): LengthAwarePaginator;
    public function profileIndex(int $clientId, array $data): LengthAwarePaginator;
}

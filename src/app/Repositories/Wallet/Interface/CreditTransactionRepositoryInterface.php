<?php

namespace App\Repositories\Wallet\Interface;

use App\Repositories\Base\Interface\EloquentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CreditTransactionRepositoryInterface extends EloquentRepositoryInterface
{
    public function indexByClientId(int $clientId): LengthAwarePaginator;

    public function sum(int $clientId): int;

    public function adminIndex(array $data): LengthAwarePaginator;

    public function profileListEverything(int $clientId): Collection;

    public function internalCloudBulkDelete(array $ids): int;

    public function internalCloudSum(array $ids): int;

    public function report(): array;
}

<?php

namespace App\Repositories\Wallet\Interface;

use App\Repositories\Base\Interface\EloquentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CreditTransactionRepositoryInterface extends EloquentRepositoryInterface
{
    public function indexByProfileId(int $profileId): LengthAwarePaginator;

    public function sum(int $profileId): int;

    public function adminIndex(array $data): LengthAwarePaginator;

    public function profileListEverything(int $profileId): Collection;

    public function internalCloudBulkDelete(array $ids): int;

    public function internalCloudSum(array $ids): int;

    public function report($from, $to): array;
}

<?php

namespace App\Repositories\Wallet\Interface;

use App\Repositories\Base\Interface\EloquentRepositoryInterface;
use Illuminate\Support\Collection;

interface CreditTransactionRepositoryInterface extends EloquentRepositoryInterface
{
    public function sum(int $profileId): int;

    public function indexEverything(int $profileId): Collection;

    public function bulkDelete(array $ids): int;

    public function internalCloudSum(array $ids): float;

    public function report($from, $to): array;
}

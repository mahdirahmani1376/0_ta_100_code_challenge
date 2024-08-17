<?php

namespace App\Repositories\Invoice\Interface;

use App\Models\Invoice;
use App\Repositories\Base\Interface\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @method Invoice update(Model $object, array $attributes, array $fillable = [])
 */
interface InvoiceRepositoryInterface extends EloquentRepositoryInterface
{
    public function indexEverything(int $profileId): Collection;

    public function prepareInvoicesForMassPayment(array $data): Collection;

    public function count(): int;

    public function countToday(): int;

    public function countPaid(): int;

    public function incomeToday(): float;

    public function reportLatest(): Collection;

    public function reportRevenue($from, $to): array;

    public function reportRevenueBasedOnGateway($from, $to): array;

    public function reportCollection($from, $to): array;

    public function rahkaranQuery($from, $to): Builder;

    public function hourlyReport($from, $to): float;

    public function findOneByCriteria($data = [], $throwException = false);
}

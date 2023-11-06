<?php

namespace App\Repositories\Invoice\Interface;

use App\Repositories\Base\Interface\EloquentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface InvoiceRepositoryInterface extends EloquentRepositoryInterface
{
    public function adminIndex(array $data): LengthAwarePaginator|Collection;

    public function profileIndex(array $data): LengthAwarePaginator;

    public function profileListEverything(int $clientId): Collection;

    public function prepareInvoicesForMassPayment(array $data): Collection;

    public function internalIndex(array $data): Collection;

    public function internalIndexMyInvoice(array $data): LengthAwarePaginator;

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
}

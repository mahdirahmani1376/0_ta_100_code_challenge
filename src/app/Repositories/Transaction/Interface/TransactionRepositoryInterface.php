<?php

namespace App\Repositories\Transaction\Interface;

use App\Models\Invoice;
use App\Models\Transaction;
use App\Repositories\Base\Interface\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface TransactionRepositoryInterface extends EloquentRepositoryInterface
{
    public function refundSuccessfulTransactions(Invoice $invoice);

    public function sumOfPaidTransactions(Invoice $invoice);

    public function getLastSuccessfulTransaction(Invoice $invoice);

    public function findByTrackingCode($trackingCode): ?Transaction;

    public function indexEverything(int $profileId): Collection;

    public function count(): int;

    public function successCount(): int;

    public function failCount(): int;

    public function reportLatest(): Collection;

    public function rahkaranQuery(): Builder;

    public function reportRevenueBasedOnGateway($from, $to): array;
}

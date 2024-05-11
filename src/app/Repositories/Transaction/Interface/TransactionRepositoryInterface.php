<?php

namespace App\Repositories\Transaction\Interface;

use App\Models\Invoice;
use App\Models\Transaction;
use App\Repositories\Base\Interface\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @method Transaction update(Model $object, array $attributes, array $fillable = [])
 */
interface TransactionRepositoryInterface extends EloquentRepositoryInterface
{
    public function refundSuccessfulTransactions(Invoice $invoice);

    public function sumOfPaidTransactions(Invoice $invoice);

    public function paidTransactions(Invoice $invoice, bool $onlinePayment = false);

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

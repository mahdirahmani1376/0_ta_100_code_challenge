<?php

namespace App\Repositories\OfflineTransaction\Interface;

use App\Models\Invoice;
use App\Models\OfflineTransaction;
use App\Models\Transaction;
use App\Repositories\Base\Interface\EloquentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface OfflineTransactionRepositoryInterface extends EloquentRepositoryInterface
{
    public function adminIndex(array $data): LengthAwarePaginator;

    public function adminIndexSimilar(OfflineTransaction $offlineTransaction): LengthAwarePaginator;

    public function sumOfVerifiedOfflineTransactions(Invoice $invoice): Collection;

    public function findByTransaction(Transaction $transaction): ?OfflineTransaction;

    public function countToday(): int;

    public function countRejected(): int;

    public function reportLatest(): Collection;
}

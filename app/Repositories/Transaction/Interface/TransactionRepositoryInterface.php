<?php

namespace App\Repositories\Transaction\Interface;

use App\Models\Invoice;
use App\Models\Transaction;
use App\Repositories\Base\Interface\EloquentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface TransactionRepositoryInterface extends EloquentRepositoryInterface
{
    public function refundSuccessfulTransactions(Invoice $invoice);

    public function sumOfPaidTransactions(Invoice $invoice);

    public function getLastSuccessfulTransaction(Invoice $invoice);

    public function findByTrackingCode($trackingCode): ?Transaction;

    public function adminIndex(array $data): Collection|LengthAwarePaginator;

    public function profileIndex(array $data): Collection|LengthAwarePaginator;

    public function profileListEverything(int $clientId): Collection;
}

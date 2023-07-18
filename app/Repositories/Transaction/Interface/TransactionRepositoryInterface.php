<?php

namespace App\Repositories\Transaction\Interface;

use App\Models\Invoice;
use App\Repositories\Base\Interface\EloquentRepositoryInterface;

interface TransactionRepositoryInterface extends EloquentRepositoryInterface
{
    public function sumOfPaidTransactions(Invoice $invoice): int;

    public function getLastSuccessfulTransaction(Invoice $invoice);
}

<?php

namespace App\Repositories\Transaction;

use App\Models\Invoice;
use App\Models\Transaction;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;

class TransactionRepository extends BaseRepository implements TransactionRepositoryInterface
{
    public string $model = Transaction::class;

    /**
     * @throws BindingResolutionException
     */
    public function sumOfPaidTransactions(Invoice $invoice): int
    {
        return $this->newQuery()
            ->where('invoice_id', $invoice->getKey())
            ->where('status', Transaction::STATUS_SUCCESS)
            ->sum('amount');
    }
}

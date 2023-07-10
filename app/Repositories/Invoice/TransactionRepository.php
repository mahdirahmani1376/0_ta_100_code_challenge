<?php

namespace App\Repositories\Invoice;

use App\Models\Transaction;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Invoice\Interface\TransactionInterface;

class TransactionRepository extends BaseRepository implements TransactionInterface
{
    public string $model = Transaction::class;
}

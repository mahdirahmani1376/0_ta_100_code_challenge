<?php

namespace App\Repositories\Transaction;

use App\Models\Transaction;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Transaction\Interface\TransactionInterface;

class TransactionRepository extends BaseRepository implements TransactionInterface
{
    public string $model = Transaction::class;
}

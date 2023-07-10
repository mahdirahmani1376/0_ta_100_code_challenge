<?php

namespace App\Repositories\Invoice;

use App\Common\Repository\BaseRepository;
use App\Models\Item;
use App\Models\Transaction;
use App\Repositories\Invoice\Interface\ItemInterface;
use App\Repositories\Invoice\Interface\TransactionInterface;

class TransactionRepository extends BaseRepository implements TransactionInterface
{
    public string $model = Transaction::class;
}

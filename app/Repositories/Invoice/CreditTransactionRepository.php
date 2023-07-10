<?php

namespace App\Repositories\Invoice;

use App\Models\CreditTransaction;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Invoice\Interface\CreditTransactionInterface;

class CreditTransactionRepository extends BaseRepository implements CreditTransactionInterface
{
    public string $model = CreditTransaction::class;
}

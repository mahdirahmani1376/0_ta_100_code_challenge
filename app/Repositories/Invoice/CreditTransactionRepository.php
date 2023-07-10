<?php

namespace App\Repositories\Invoice;

use App\Common\Repository\BaseRepository;
use App\Models\CreditTransaction;
use App\Repositories\Invoice\Interface\CreditTransactionInterface;

class CreditTransactionRepository extends BaseRepository implements CreditTransactionInterface
{
    public string $model = CreditTransaction::class;
}

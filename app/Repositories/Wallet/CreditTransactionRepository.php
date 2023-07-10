<?php

namespace App\Repositories\Wallet;

use App\Models\CreditTransaction;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Wallet\Interface\CreditTransactionInterface;

class CreditTransactionRepository extends BaseRepository implements CreditTransactionInterface
{
    public string $model = CreditTransaction::class;
}

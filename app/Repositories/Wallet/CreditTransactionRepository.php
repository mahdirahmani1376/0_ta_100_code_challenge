<?php

namespace App\Repositories\Wallet;

use App\Models\CreditTransaction;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Wallet\Interface\CreditTransactionRepositoryInterface;

class CreditTransactionRepository extends BaseRepository implements CreditTransactionRepositoryInterface
{
    public string $model = CreditTransaction::class;
}

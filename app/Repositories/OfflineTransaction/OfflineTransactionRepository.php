<?php

namespace App\Repositories\OfflineTransaction;

use App\Models\OfflineTransaction;
use App\Repositories\Base\BaseRepository;
use App\Repositories\OfflineTransaction\Interface\OfflineTransactionInterface;

class OfflineTransactionRepository extends BaseRepository implements OfflineTransactionInterface
{
    public string $model = OfflineTransaction::class;
}

<?php

namespace App\Repositories\OfflineTransaction;

use App\Models\OfflineTransaction;
use App\Repositories\Base\BaseRepository;
use App\Repositories\OfflineTransaction\Interface\OfflineTransactionRepositoryInterface;

class OfflineTransactionRepository extends BaseRepository implements OfflineTransactionRepositoryInterface
{
    public string $model = OfflineTransaction::class;
}

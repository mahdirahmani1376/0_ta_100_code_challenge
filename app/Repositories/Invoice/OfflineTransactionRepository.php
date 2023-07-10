<?php

namespace App\Repositories\Invoice;

use App\Common\Repository\BaseRepository;
use App\Models\OfflineTransaction;
use App\Repositories\Invoice\Interface\OfflineTransactionInterface;

class OfflineTransactionRepository extends BaseRepository implements OfflineTransactionInterface
{
    public string $model = OfflineTransaction::class;
}

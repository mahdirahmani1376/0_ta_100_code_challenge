<?php

namespace App\Http\Controllers\Admin\OfflineTransaction;

use App\Http\Resources\Admin\OfflineTransaction\OfflineTransactionResource;
use App\Models\OfflineTransaction;

class ShowOfflineTransactionController
{
    public function __invoke(OfflineTransaction $offlineTransaction)
    {
        return OfflineTransactionResource::make($offlineTransaction);
    }
}

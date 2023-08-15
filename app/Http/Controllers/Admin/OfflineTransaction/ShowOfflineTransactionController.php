<?php

namespace App\Http\Controllers\Admin\OfflineTransaction;

use App\Http\Resources\Admin\OfflineTransaction\OfflineTransactionResource;
use App\Http\Resources\Admin\OfflineTransaction\ShowOfflineTransactionResource;
use App\Models\OfflineTransaction;

class ShowOfflineTransactionController
{
    /**
     * @param OfflineTransaction $offlineTransaction
     * @return OfflineTransactionResource
     */
    public function __invoke(OfflineTransaction $offlineTransaction)
    {
        return ShowOfflineTransactionResource::make($offlineTransaction);
    }
}

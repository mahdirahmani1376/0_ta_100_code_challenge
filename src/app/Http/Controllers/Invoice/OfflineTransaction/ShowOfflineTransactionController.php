<?php

namespace App\Http\Controllers\Invoice\OfflineTransaction;

use App\Http\Resources\Invoice\OfflineTransaction\OfflineTransactionResource;
use App\Http\Resources\Invoice\OfflineTransaction\ShowOfflineTransactionResource;
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

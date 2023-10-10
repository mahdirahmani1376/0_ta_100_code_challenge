<?php

namespace App\Http\Controllers\Internal\Cloud\Wallet;

use App\Http\Resources\Internal\Cloud\Wallet\ShowCreditTransactionResource;
use App\Models\CreditTransaction;

class ShowCreditTransactionController
{
    public function __invoke(CreditTransaction $creditTransaction)
    {
        return ShowCreditTransactionResource::make($creditTransaction);
    }
}

<?php

namespace App\Http\Controllers\Wallet\CreditTransaction;

use App\Http\Resources\Wallet\CreditTransaction\ShowCreditTransactionResource;
use App\Models\CreditTransaction;

class ShowCreditTransactionController
{
    public function __invoke(CreditTransaction $creditTransaction)
    {
        return ShowCreditTransactionResource::make($creditTransaction);
    }
}

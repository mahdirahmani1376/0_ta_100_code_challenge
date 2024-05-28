<?php

namespace App\Http\Controllers\Wallet\CreditTransaction;

use App\Http\Controllers\Controller;
use App\Http\Resources\Wallet\CreditTransaction\ShowCreditTransactionResource;
use App\Models\CreditTransaction;

class ShowCreditTransactionController extends Controller
{
    public function __invoke(CreditTransaction $creditTransaction)
    {
        return ShowCreditTransactionResource::make($creditTransaction);
    }
}

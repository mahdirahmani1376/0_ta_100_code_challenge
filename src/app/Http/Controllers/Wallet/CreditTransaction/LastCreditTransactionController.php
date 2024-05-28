<?php

namespace App\Http\Controllers\Wallet\CreditTransaction;

use App\Actions\Wallet\CreditTransaction\LastCreditTransactionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Wallet\CreditTransaction\LastCreditTransactionRequest;

class LastCreditTransactionController extends Controller
{
    public function __construct(
        public LastCreditTransactionAction $lastCreditTransactionAction
    )
    {
        parent::__construct();
    }

    public function __invoke(LastCreditTransactionRequest $request)
    {
        return response()->json(data: [
            'credit' => ($this->lastCreditTransactionAction)($request->validated())
        ]);
    }
}

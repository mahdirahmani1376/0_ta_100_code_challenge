<?php

namespace App\Http\Controllers\Admin\Wallet;

use App\Actions\Admin\Wallet\IndexCreditTransactionAction;
use App\Http\Requests\Admin\Wallet\IndexCreditTransactionRequest;
use App\Http\Resources\Admin\Wallet\CreditTransactionResource;

class IndexCreditTransactionController
{
    private IndexCreditTransactionAction $indexCreditTransactionAction;

    public function __construct(IndexCreditTransactionAction $indexCreditTransactionAction)
    {
        $this->indexCreditTransactionAction = $indexCreditTransactionAction;
    }

    public function __invoke(IndexCreditTransactionRequest $request)
    {
        $creditTransactions = ($this->indexCreditTransactionAction)($request->validated());

        return CreditTransactionResource::collection($creditTransactions);
    }
}

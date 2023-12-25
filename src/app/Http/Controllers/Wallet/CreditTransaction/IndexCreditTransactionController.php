<?php

namespace App\Http\Controllers\Wallet\CreditTransaction;

use App\Actions\Wallet\CreditTransaction\IndexCreditTransactionAction;
use App\Http\Requests\Wallet\CreditTransaction\IndexCreditTransactionRequest;
use App\Http\Resources\Wallet\CreditTransaction\CreditTransactionResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexCreditTransactionController
{
    public function __construct(private readonly IndexCreditTransactionAction $indexCreditTransactionAction)
    {
    }

    /**
     * @param IndexCreditTransactionRequest $request
     * @return AnonymousResourceCollection
     */
    public function __invoke(IndexCreditTransactionRequest $request)
    {
        $creditTransactions = ($this->indexCreditTransactionAction)($request->validated());

        return CreditTransactionResource::collection($creditTransactions);
    }
}

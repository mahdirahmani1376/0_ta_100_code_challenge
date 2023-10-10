<?php

namespace App\Http\Controllers\Admin\Wallet;

use App\Actions\Admin\Wallet\IndexCreditTransactionAction;
use App\Http\Requests\Admin\Wallet\IndexCreditTransactionRequest;
use App\Http\Resources\Admin\Wallet\CreditTransactionResource;
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

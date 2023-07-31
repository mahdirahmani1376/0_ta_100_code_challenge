<?php

namespace App\Http\Controllers\Profile\Transaction;

use App\Actions\Profile\Transaction\IndexTransactionAction;
use App\Http\Requests\Profile\Transaction\IndexTransactionRequest;
use App\Http\Resources\Profile\Transaction\TransactionResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexTransactionController
{
    private IndexTransactionAction $indexTransactionAction;

    public function __construct(IndexTransactionAction $indexTransactionAction)
    {
        $this->indexTransactionAction = $indexTransactionAction;
    }

    /**
     * @param IndexTransactionRequest $request
     * @return AnonymousResourceCollection
     */
    public function __invoke(IndexTransactionRequest $request)
    {
        $transactions = ($this->indexTransactionAction)($request->validated());

        return TransactionResource::collection($transactions);
    }
}

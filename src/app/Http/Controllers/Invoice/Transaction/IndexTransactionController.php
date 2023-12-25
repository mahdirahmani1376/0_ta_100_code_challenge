<?php

namespace App\Http\Controllers\Invoice\Transaction;

use App\Actions\Invoice\Transaction\IndexTransactionAction;
use App\Http\Requests\Invoice\Transaction\IndexTransactionRequest;
use App\Http\Resources\Invoice\Transaction\TransactionResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexTransactionController
{
    public function __construct(private readonly IndexTransactionAction $indexTransactionAction)
    {
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

<?php

namespace App\Http\Controllers\Admin\Invoice\Transaction;

use App\Actions\Admin\Invoice\Transaction\IndexTransactionAction;
use App\Http\Requests\Admin\Invoice\Transaction\IndexTransactionRequest;
use App\Http\Resources\Admin\Transaction\TransactionResource;

class IndexTransactionController
{
    private IndexTransactionAction $indexTransactionAction;

    public function __construct(IndexTransactionAction $indexTransactionAction)
    {
        $this->indexTransactionAction = $indexTransactionAction;
    }

    public function __invoke(IndexTransactionRequest $request)
    {
        $transactions = ($this->indexTransactionAction)($request->validated());

        return TransactionResource::collection($transactions);
    }
}

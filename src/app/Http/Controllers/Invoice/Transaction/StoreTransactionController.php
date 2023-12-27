<?php

namespace App\Http\Controllers\Invoice\Transaction;

use App\Actions\Invoice\Transaction\StoreTransactionAction;
use App\Http\Requests\Invoice\Transaction\StoreTransactionRequest;
use App\Http\Resources\Invoice\Transaction\TransactionResource;

class StoreTransactionController
{
    public function __construct(private readonly StoreTransactionAction $storeTransactionAction)
    {
    }

    /**
     * @param StoreTransactionRequest $request
     * @return TransactionResource
     */
    public function __invoke(StoreTransactionRequest $request)
    {
        $transaction = ($this->storeTransactionAction)($request->validated());

        return TransactionResource::make($transaction);
    }
}

<?php

namespace App\Http\Controllers\Admin\Invoice\Transaction;

use App\Actions\Admin\Invoice\Transaction\StoreTransactionAction;
use App\Http\Requests\Admin\Transaction\StoreTransactionRequest;
use App\Http\Resources\Admin\Transaction\TransactionResource;
use App\Models\Invoice;

class StoreTransactionController
{
    private StoreTransactionAction $storeTransactionAction;

    public function __construct(StoreTransactionAction $storeTransactionAction)
    {
        $this->storeTransactionAction = $storeTransactionAction;
    }

    public function __invoke(Invoice $invoice, StoreTransactionRequest $request)
    {
        $transaction = ($this->storeTransactionAction)($invoice, $request->validated());

        return TransactionResource::make($transaction);
    }
}

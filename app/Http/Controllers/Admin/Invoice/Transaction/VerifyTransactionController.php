<?php

namespace App\Http\Controllers\Admin\Invoice\Transaction;

use App\Actions\Admin\Invoice\Transaction\VerifyTransactionAction;
use App\Http\Requests\Admin\Transaction\StoreTransactionRequest;
use App\Http\Requests\Admin\Transaction\VerifyTransactionRequest;
use App\Http\Resources\Admin\Transaction\TransactionResource;
use App\Models\Invoice;
use App\Models\Transaction;

class VerifyTransactionController
{
    private VerifyTransactionAction $verifyTransactionAction;

    public function __construct(VerifyTransactionAction $verifyTransactionAction)
    {
        $this->verifyTransactionAction = $verifyTransactionAction;
    }

    /**
     * @param Invoice $invoice
     * @param StoreTransactionRequest $request
     * @return TransactionResource
     */
    public function __invoke(Transaction $transaction, VerifyTransactionRequest $request)
    {
        $transaction = ($this->verifyTransactionAction)($transaction);

        return TransactionResource::make($transaction);
    }
}

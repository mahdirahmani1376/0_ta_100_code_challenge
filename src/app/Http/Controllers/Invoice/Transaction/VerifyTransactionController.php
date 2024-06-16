<?php

namespace App\Http\Controllers\Invoice\Transaction;

use App\Actions\Invoice\Transaction\VerifyTransactionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\Transaction\VerifyTransactionRequest;
use App\Http\Resources\Invoice\Transaction\TransactionResource;
use App\Models\Transaction;

class VerifyTransactionController extends Controller
{
    public function __construct(private readonly VerifyTransactionAction $verifyTransactionAction)
    {
        parent::__construct();
    }

    /**
     * @param Transaction $transaction
     * @param VerifyTransactionRequest $request
     * @return TransactionResource
     */
    public function __invoke(Transaction $transaction, VerifyTransactionRequest $request)
    {
        $transaction = ($this->verifyTransactionAction)($transaction);

        return TransactionResource::make($transaction);
    }
}

<?php

namespace App\Http\Controllers\Wallet\CreditTransaction;

use App\Actions\Wallet\CreditTransaction\IndexCreditTransactionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Wallet\CreditTransaction\IndexCreditTransactionRequest;
use App\Http\Resources\Wallet\CreditTransaction\CreditTransactionResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexCreditTransactionController extends Controller
{
    public function __construct(private readonly IndexCreditTransactionAction $indexCreditTransactionAction)
    {
        parent::__construct();
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

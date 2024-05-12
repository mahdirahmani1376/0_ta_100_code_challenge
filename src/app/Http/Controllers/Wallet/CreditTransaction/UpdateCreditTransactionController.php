<?php

namespace App\Http\Controllers\Wallet\CreditTransaction;

use App\Actions\Wallet\CreditTransaction\UpdateCreditTransactionAction;
use App\Http\Requests\Wallet\CreditTransaction\UpdateCreditTransactionRequest;
use App\Http\Resources\Wallet\CreditTransaction\ShowCreditTransactionResource;
use App\Models\CreditTransaction;

class UpdateCreditTransactionController
{
    public function __construct(
        private readonly UpdateCreditTransactionAction $updateCreditTransactionAction,
    )
    {
    }

    public function __invoke(CreditTransaction $creditTransaction, UpdateCreditTransactionRequest $request)
    {
        return ShowCreditTransactionResource::make(
            ($this->updateCreditTransactionAction)($creditTransaction, $request->validated())
        );
    }
}

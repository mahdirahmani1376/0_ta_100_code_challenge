<?php

namespace App\Http\Controllers\Wallet\CreditTransaction;

use App\Actions\Wallet\CreditTransaction\UpdateCreditTransactionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Wallet\CreditTransaction\UpdateCreditTransactionRequest;
use App\Http\Resources\Wallet\CreditTransaction\ShowCreditTransactionResource;
use App\Models\CreditTransaction;

class UpdateCreditTransactionController extends Controller
{
    public function __construct(
        private readonly UpdateCreditTransactionAction $updateCreditTransactionAction,
    )
    {
        parent::__construct();
    }

    public function __invoke(CreditTransaction $creditTransaction, UpdateCreditTransactionRequest $request)
    {
        return ShowCreditTransactionResource::make(
            ($this->updateCreditTransactionAction)($creditTransaction, $request->validated())
        );
    }
}

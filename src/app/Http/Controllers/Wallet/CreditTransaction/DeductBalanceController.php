<?php

namespace App\Http\Controllers\Wallet\CreditTransaction;

use App\Actions\Wallet\CreditTransaction\DeductBalanceAction;
use App\Http\Requests\Wallet\CreditTransaction\DeductBalanceRequest;
use App\Http\Resources\Wallet\CreditTransaction\CreditTransactionResource;

class DeductBalanceController
{
    public function __construct(private readonly DeductBalanceAction $deductBalanceAction)
    {
    }

    /**
     * @param int $profileId
     * @param DeductBalanceRequest $request
     * @return CreditTransactionResource
     */
    public function __invoke(int $profileId, DeductBalanceRequest $request)
    {
        $creditTransaction = ($this->deductBalanceAction)($profileId, $request->validated());

        return CreditTransactionResource::make($creditTransaction);
    }
}

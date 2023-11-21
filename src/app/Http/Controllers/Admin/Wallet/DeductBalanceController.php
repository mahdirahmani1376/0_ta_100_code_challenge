<?php

namespace App\Http\Controllers\Admin\Wallet;

use App\Actions\Admin\Wallet\DeductBalanceAction;
use App\Http\Requests\Admin\Wallet\DeductBalanceRequest;
use App\Http\Resources\Admin\Wallet\CreditTransactionResource;

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

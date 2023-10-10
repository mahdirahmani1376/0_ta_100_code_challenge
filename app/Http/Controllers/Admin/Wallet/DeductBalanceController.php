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
     * @param int $clientId
     * @param DeductBalanceRequest $request
     * @return CreditTransactionResource
     */
    public function __invoke(int $clientId, DeductBalanceRequest $request)
    {
        $creditTransaction = ($this->deductBalanceAction)($clientId, $request->validated());

        return CreditTransactionResource::make($creditTransaction);
    }
}

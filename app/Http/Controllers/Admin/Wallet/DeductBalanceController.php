<?php

namespace App\Http\Controllers\Admin\Wallet;

use App\Actions\Admin\Wallet\DeductBalanceAction;
use App\Http\Requests\Admin\Wallet\DeductBalanceRequest;
use App\Http\Resources\Admin\Wallet\CreditTransactionResource;

class DeductBalanceController
{
    private DeductBalanceAction $deductBalanceAction;

    public function __construct(DeductBalanceAction $deductBalanceAction)
    {
        $this->deductBalanceAction = $deductBalanceAction;
    }

    public function __invoke(int $clientId, DeductBalanceRequest $request)
    {
        $creditTransaction = ($this->deductBalanceAction)($clientId, $request->validated());

        return CreditTransactionResource::make($creditTransaction);
    }
}

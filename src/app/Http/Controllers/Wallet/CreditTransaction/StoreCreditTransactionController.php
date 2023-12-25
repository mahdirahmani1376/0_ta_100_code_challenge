<?php

namespace App\Http\Controllers\Wallet\CreditTransaction;

use App\Actions\Wallet\CreditTransaction\StoreCreditTransactionAction;
use App\Http\Requests\Wallet\CreditTransaction\StoreCreditTransactionRequest;
use App\Http\Resources\Wallet\CreditTransaction\CreditTransactionResource;

class StoreCreditTransactionController
{
    public function __construct(private readonly StoreCreditTransactionAction $storeCreditTransactionAction)
    {
    }

    /**
     * @param int $profileId
     * @param StoreCreditTransactionRequest $request
     * @return CreditTransactionResource
     */
    public function __invoke(int $profileId, StoreCreditTransactionRequest $request)
    {
        $creditTransaction = ($this->storeCreditTransactionAction)($profileId, $request->validated());

        return CreditTransactionResource::make($creditTransaction);
    }
}

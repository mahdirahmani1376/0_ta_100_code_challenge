<?php

namespace App\Http\Controllers\Admin\Wallet;

use App\Actions\Admin\Wallet\StoreCreditTransactionAction;
use App\Http\Requests\Admin\Wallet\StoreCreditTransactionRequest;
use App\Http\Resources\Admin\Wallet\CreditTransactionResource;

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

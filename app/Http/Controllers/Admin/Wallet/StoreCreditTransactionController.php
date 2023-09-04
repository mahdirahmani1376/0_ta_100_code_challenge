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
     * @param int $clientId
     * @param StoreCreditTransactionRequest $request
     * @return CreditTransactionResource
     */
    public function __invoke(int $clientId, StoreCreditTransactionRequest $request)
    {
        $creditTransaction = ($this->storeCreditTransactionAction)($clientId, $request->validated());

        return CreditTransactionResource::make($creditTransaction);
    }
}

<?php

namespace App\Http\Controllers\Internal\Cloud\Wallet;

use App\Actions\Internal\Cloud\Wallet\StoreCreditTransactionAction;
use App\Http\Requests\Internal\Cloud\Wallet\StoreCreditTransactionRequest;
use App\Http\Resources\Internal\Cloud\Wallet\ShowCreditTransactionResource;

class StoreCreditTransactionController
{
    public function __construct(private readonly StoreCreditTransactionAction $storeCreditTransactionAction)
    {
    }

    public function __invoke(StoreCreditTransactionRequest $request)
    {
        $creditTransaction = ($this->storeCreditTransactionAction)($request->validated('client_id'), $request->validated());

        return ShowCreditTransactionResource::make($creditTransaction);
    }
}

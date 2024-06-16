<?php

namespace App\Http\Controllers\Wallet\CreditTransaction;

use App\Actions\Wallet\CreditTransaction\StoreCreditTransactionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Wallet\CreditTransaction\StoreCreditTransactionRequest;
use App\Http\Resources\Wallet\CreditTransaction\CreditTransactionResource;

class StoreCreditTransactionController extends Controller
{
    public function __construct(private readonly StoreCreditTransactionAction $storeCreditTransactionAction)
    {
        parent::__construct();
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

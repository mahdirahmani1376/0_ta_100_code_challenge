<?php

namespace App\Http\Controllers\BankGateway\DirectPayment;

use App\Actions\BankGateway\DirectPayment\RequestBazaarPayContractAction;
use App\Http\Requests\BankGateway\DirectPayment\StoreBazaarPayDirectPaymentRequest;

class RequestBazaarPayContractController
{
    public function __construct(private readonly RequestBazaarPayContractAction $bazaarPayContractAction)
    {
    }

    public function __invoke(StoreBazaarPayDirectPaymentRequest $request)
    {
        $contractToken = ($this->bazaarPayContractAction)($request->validated());

        return response()->json(['data' => ['contract_token' => $contractToken,]]);
    }
}

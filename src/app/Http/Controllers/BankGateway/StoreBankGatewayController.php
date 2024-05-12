<?php

namespace App\Http\Controllers\BankGateway;

use App\Actions\BankGateway\StoreBankGatewayAction;
use App\Http\Requests\BankGateway\StoreBankGatewayRequest;
use App\Http\Resources\BankGateway\BankGatewayResource;

class StoreBankGatewayController
{
    public function __construct(private readonly StoreBankGatewayAction $storeBankGatewayAction)
    {
    }

    /**
     * @param StoreBankGatewayRequest $request
     * @return BankGatewayResource
     */
    public function __invoke(StoreBankGatewayRequest $request)
    {
        $bankGateway = ($this->storeBankGatewayAction)($request->validated());

        return BankGatewayResource::make($bankGateway);
    }
}

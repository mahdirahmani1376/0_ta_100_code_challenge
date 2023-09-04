<?php

namespace App\Http\Controllers\Admin\BankGateway;

use App\Actions\Admin\BankGateway\StoreBankGatewayAction;
use App\Http\Requests\Admin\BankGateway\StoreBankGatewayRequest;
use App\Http\Resources\Admin\BankGateway\BankGatewayResource;

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

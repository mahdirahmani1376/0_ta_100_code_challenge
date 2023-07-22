<?php

namespace App\Http\Controllers\Admin\BankGateway;

use App\Actions\Admin\BankGateway\StoreBankGatewayAction;
use App\Http\Requests\Admin\BankGateway\StoreBankGatewayRequest;
use App\Http\Resources\Admin\BankGateway\BankGatewayResource;

class StoreBankGatewayController
{
    private StoreBankGatewayAction $storeBankGatewayAction;

    public function __construct(StoreBankGatewayAction $storeBankGatewayAction)
    {
        $this->storeBankGatewayAction = $storeBankGatewayAction;
    }

    public function __invoke(StoreBankGatewayRequest $request)
    {
        $bankGateway = ($this->storeBankGatewayAction)($request->validated());

        return BankGatewayResource::make($bankGateway);
    }
}

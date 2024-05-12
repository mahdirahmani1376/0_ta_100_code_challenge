<?php

namespace App\Http\Controllers\BankGateway;

use App\Actions\BankGateway\UpdateBankGatewayAction;
use App\Http\Requests\BankGateway\UpdateBankGatewayRequest;
use App\Http\Resources\BankGateway\BankGatewayResource;
use App\Models\BankGateway;

class UpdateBankGatewayController
{
    public function __construct(private readonly UpdateBankGatewayAction $updateBankGatewayAction)
    {
    }

    /**
     * @param UpdateBankGatewayRequest $request
     * @param BankGateway $bankGateway
     * @return BankGatewayResource
     */
    public function __invoke(UpdateBankGatewayRequest $request, BankGateway $bankGateway)
    {
        $bankGateway = ($this->updateBankGatewayAction)($bankGateway, $request->validated());

        return BankGatewayResource::make($bankGateway);
    }
}

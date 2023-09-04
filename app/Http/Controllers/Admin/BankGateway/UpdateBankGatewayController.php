<?php

namespace App\Http\Controllers\Admin\BankGateway;

use App\Actions\Admin\BankGateway\UpdateBankGatewayAction;
use App\Http\Requests\Admin\BankGateway\UpdateBankGatewayRequest;
use App\Http\Resources\Admin\BankGateway\BankGatewayResource;
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

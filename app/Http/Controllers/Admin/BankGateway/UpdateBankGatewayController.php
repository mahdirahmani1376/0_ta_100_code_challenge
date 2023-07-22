<?php

namespace App\Http\Controllers\Admin\BankGateway;

use App\Actions\Admin\BankGateway\UpdateBankGatewayAction;
use App\Http\Requests\Admin\BankGateway\UpdateBankGatewayRequest;
use App\Http\Resources\Admin\BankGateway\BankGatewayResource;
use App\Models\BankGateway;

class UpdateBankGatewayController
{

    private UpdateBankGatewayAction $updateBankGatewayAction;

    public function __construct(UpdateBankGatewayAction $updateBankGatewayAction)
    {
        $this->updateBankGatewayAction = $updateBankGatewayAction;
    }

    public function __invoke(UpdateBankGatewayRequest $request, BankGateway $bankGateway)
    {
        $bankGateway = ($this->updateBankGatewayAction)($bankGateway, $request->validated());

        return BankGatewayResource::make($bankGateway);
    }
}

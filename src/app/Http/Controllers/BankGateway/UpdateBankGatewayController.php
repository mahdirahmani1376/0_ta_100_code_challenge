<?php

namespace App\Http\Controllers\BankGateway;

use App\Actions\BankGateway\UpdateBankGatewayAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\BankGateway\UpdateBankGatewayRequest;
use App\Http\Resources\BankGateway\BankGatewayResource;
use App\Models\BankGateway;

class UpdateBankGatewayController extends Controller
{
    public function __construct(private readonly UpdateBankGatewayAction $updateBankGatewayAction)
    {
        parent::__construct();
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

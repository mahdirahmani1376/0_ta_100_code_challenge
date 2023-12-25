<?php

namespace App\Http\Controllers\BankGateway;

use App\Http\Resources\BankGateway\BankGatewayResource;
use App\Models\BankGateway;

class ShowBankGatewayController
{
    /**
     * @param BankGateway $bankGateway
     * @return BankGatewayResource
     */
    public function __invoke(BankGateway $bankGateway)
    {
        return BankGatewayResource::make($bankGateway);
    }
}

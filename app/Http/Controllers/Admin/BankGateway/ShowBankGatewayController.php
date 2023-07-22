<?php

namespace App\Http\Controllers\Admin\BankGateway;

use App\Http\Resources\Admin\BankGateway\BankGatewayResource;
use App\Models\BankGateway;

class ShowBankGatewayController
{
    public function __invoke(BankGateway $bankGateway)
    {
        return BankGatewayResource::make($bankGateway);
    }
}

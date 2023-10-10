<?php

namespace App\Http\Controllers\Public\BankGateway;

use App\Actions\Public\BankGateway\IndexBankGatewayAction;
use App\Http\Resources\Public\BankGateway\BankGatewayResource;

class IndexBankGatewayController
{
    public function __construct(private readonly IndexBankGatewayAction $indexBankGatewayAction)
    {
    }

    public function __invoke()
    {
        $bankGateways = ($this->indexBankGatewayAction)();

        return BankGatewayResource::collection($bankGateways);
    }
}

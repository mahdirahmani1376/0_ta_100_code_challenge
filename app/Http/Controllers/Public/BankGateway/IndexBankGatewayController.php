<?php

namespace App\Http\Controllers\Public\BankGateway;

use App\Actions\Public\BankGateway\IndexBankGatewayAction;
use App\Http\Resources\Public\BankGateway\BankGatewayResource;

class IndexBankGatewayController
{
    private IndexBankGatewayAction $indexBankGatewayAction;

    public function __construct(IndexBankGatewayAction $indexBankGatewayAction)
    {
        $this->indexBankGatewayAction = $indexBankGatewayAction;
    }

    public function __invoke()
    {
        $bankGateways = ($this->indexBankGatewayAction)();

        return BankGatewayResource::collection($bankGateways);
    }
}

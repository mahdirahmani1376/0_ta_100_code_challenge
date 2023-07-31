<?php

namespace App\Http\Controllers\Public\BankGateway;

use App\Actions\Public\BankGateway\IndexBankGatewayAction;
use App\Http\Resources\Public\BankGateway\BankGatewayResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexBankGatewayController
{
    private IndexBankGatewayAction $indexBankGatewayAction;

    public function __construct(IndexBankGatewayAction $indexBankGatewayAction)
    {
        $this->indexBankGatewayAction = $indexBankGatewayAction;
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function __invoke()
    {
        $bankGateways = ($this->indexBankGatewayAction)();

        return BankGatewayResource::collection($bankGateways);
    }
}

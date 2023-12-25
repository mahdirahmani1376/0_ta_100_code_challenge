<?php

namespace App\Http\Controllers\BankGateway;

use App\Actions\BankGateway\IndexBankGatewayAction;
use App\Http\Requests\BankGateway\IndexBankGatewayRequest;
use App\Http\Resources\BankGateway\BankGatewayResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexBankGatewayController
{
    public function __construct(private readonly IndexBankGatewayAction $indexBankGatewayAction)
    {
    }

    /**
     * @param IndexBankGatewayRequest $request
     * @return AnonymousResourceCollection
     */
    public function __invoke(IndexBankGatewayRequest $request)
    {
        $bankGateways = ($this->indexBankGatewayAction)($request->validated());

        return BankGatewayResource::collection($bankGateways);
    }
}

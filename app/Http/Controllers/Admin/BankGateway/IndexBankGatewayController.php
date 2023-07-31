<?php

namespace App\Http\Controllers\Admin\BankGateway;

use App\Actions\Admin\BankGateway\IndexBankGatewayAction;
use App\Http\Requests\Admin\BankGateway\IndexBankGatewayRequest;
use App\Http\Resources\Admin\BankGateway\BankGatewayResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexBankGatewayController
{
    private IndexBankGatewayAction $indexBankGatewayAction;

    public function __construct(IndexBankGatewayAction $indexBankGatewayAction)
    {
        $this->indexBankGatewayAction = $indexBankGatewayAction;
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

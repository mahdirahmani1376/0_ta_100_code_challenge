<?php

namespace App\Http\Controllers\ClientCashout;

use App\Actions\ClientCashout\StoreClientCashoutAction;
use App\Http\Requests\ClientCashout\StoreClientCashoutRequest;
use App\Http\Resources\ClientCashout\ClientCashoutResource;

class StoreClientCashoutController
{
    public function __construct(private readonly StoreClientCashoutAction $storeClientCashoutAction)
    {
    }

    /**
     * @param StoreClientCashoutRequest $request
     * @return ClientCashoutResource
     */
    public function __invoke(StoreClientCashoutRequest $request)
    {
        $clientCashout = ($this->storeClientCashoutAction)($request->validated());

        return ClientCashoutResource::make($clientCashout);
    }
}

<?php

namespace App\Http\Controllers\Admin\ClientCashout;

use App\Actions\Admin\ClientCashout\StoreClientCashoutAction;
use App\Http\Requests\Admin\ClientCashout\StoreClientCashoutRequest;
use App\Http\Resources\Admin\ClientCashout\ClientCashoutResource;

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

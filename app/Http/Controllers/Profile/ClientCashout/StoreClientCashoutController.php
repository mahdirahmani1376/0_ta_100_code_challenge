<?php

namespace App\Http\Controllers\Profile\ClientCashout;

use App\Actions\Profile\ClientCashout\StoreClientCashoutAction;
use App\Http\Requests\Profile\ClientCashout\StoreClientCashoutRequest;
use App\Http\Resources\Profile\ClientCashout\ClientCashoutResource;

class StoreClientCashoutController
{
    private StoreClientCashoutAction $storeClientCashoutAction;

    public function __construct(StoreClientCashoutAction $storeClientCashoutAction)
    {
        $this->storeClientCashoutAction = $storeClientCashoutAction;
    }

    /**
     * @param StoreClientCashoutRequest $request
     * @return ClientCashoutResource
     */
    public function __invoke(StoreClientCashoutRequest $request)
    {
        $clientCashout = ($this->storeClientCashoutAction)(request('client_id'), $request->validated());

        return ClientCashoutResource::make($clientCashout);
    }
}

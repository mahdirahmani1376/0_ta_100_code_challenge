<?php

namespace App\Http\Controllers\Profile\ClientCashout;

use App\Actions\Profile\ClientCashout\StoreClientCashoutAction;
use App\Http\Requests\Profile\ClientCashout\StoreClientCashoutRequest;
use App\Http\Resources\Profile\ClientCashout\ClientCashoutResource;

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
        $clientCashout = ($this->storeClientCashoutAction)(request('profile_id'), $request->validated());

        return ClientCashoutResource::make($clientCashout);
    }
}

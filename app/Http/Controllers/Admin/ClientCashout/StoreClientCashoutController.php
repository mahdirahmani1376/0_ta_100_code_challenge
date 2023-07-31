<?php

namespace App\Http\Controllers\Admin\ClientCashout;

use App\Actions\Admin\ClientCashout\StoreClientCashoutAction;
use App\Http\Requests\Admin\ClientCashout\StoreClientCashoutRequest;
use App\Http\Resources\Admin\ClientCashout\ClientCashoutResource;

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
        $clientCashout = ($this->storeClientCashoutAction)($request->validated());

        return ClientCashoutResource::make($clientCashout);
    }
}

<?php

namespace App\Http\Controllers\Profile\ClientCashout;

use App\Actions\Profile\ClientCashout\UpdateClientCashoutAction;
use App\Http\Requests\Profile\ClientCashout\UpdateClientCashoutRequest;
use App\Http\Resources\Profile\ClientCashout\ClientCashoutResource;
use App\Models\ClientCashout;

class UpdateClientCashoutController
{
    private UpdateClientCashoutAction $updateClientCashoutAction;

    public function __construct(UpdateClientCashoutAction $updateClientCashoutAction)
    {
        $this->updateClientCashoutAction = $updateClientCashoutAction;
    }

    /**
     * @param ClientCashout $clientCashout
     * @param UpdateClientCashoutRequest $request
     * @return ClientCashoutResource
     */
    public function __invoke(ClientCashout $clientCashout, UpdateClientCashoutRequest $request)
    {
        $clientCashout = ($this->updateClientCashoutAction)($clientCashout, $request->validated());

        return ClientCashoutResource::make($clientCashout);
    }
}

<?php

namespace App\Http\Controllers\ClientCashout;

use App\Actions\ClientCashout\UpdateClientCashoutAction;
use App\Http\Requests\ClientCashout\UpdateClientCashoutRequest;
use App\Http\Resources\ClientCashout\ClientCashoutResource;
use App\Models\ClientCashout;

class UpdateClientCashoutController
{
    public function __construct(private readonly UpdateClientCashoutAction $updateClientCashoutAction)
    {
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

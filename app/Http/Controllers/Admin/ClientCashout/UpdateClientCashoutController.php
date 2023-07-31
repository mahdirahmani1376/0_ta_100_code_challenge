<?php

namespace App\Http\Controllers\Admin\ClientCashout;

use App\Actions\Admin\ClientCashout\UpdateClientCashoutAction;
use App\Http\Requests\Admin\ClientCashout\UpdateClientCashoutRequest;
use App\Http\Resources\Admin\ClientCashout\ClientCashoutResource;
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

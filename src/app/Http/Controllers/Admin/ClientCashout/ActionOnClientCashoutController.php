<?php

namespace App\Http\Controllers\Admin\ClientCashout;

use App\Actions\Admin\ClientCashout\ActionOnClientCashoutAction;
use App\Http\Requests\Admin\ClientCashout\ActionOnClientCashoutRequest;
use App\Http\Resources\Admin\ClientCashout\ClientCashoutResource;
use App\Models\ClientCashout;

class ActionOnClientCashoutController
{
    public function __construct(private readonly ActionOnClientCashoutAction $actionOnClientCashoutAction)
    {
    }

    public function __invoke(ClientCashout $clientCashout, string $action, ActionOnClientCashoutRequest $request)
    {
        return ClientCashoutResource::make(($this->actionOnClientCashoutAction)($clientCashout, $action, $request->validated()));
    }
}

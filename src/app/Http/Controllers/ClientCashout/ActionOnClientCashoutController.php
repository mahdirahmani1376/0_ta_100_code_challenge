<?php

namespace App\Http\Controllers\ClientCashout;

use App\Actions\ClientCashout\ActionOnClientCashoutAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClientCashout\ActionOnClientCashoutRequest;
use App\Http\Resources\ClientCashout\ClientCashoutResource;
use App\Models\ClientCashout;

class ActionOnClientCashoutController extends Controller
{
    public function __construct(private readonly ActionOnClientCashoutAction $actionOnClientCashoutAction)
    {
        parent::__construct();
    }

    public function __invoke(ClientCashout $clientCashout, string $action, ActionOnClientCashoutRequest $request)
    {
        return ClientCashoutResource::make(($this->actionOnClientCashoutAction)($clientCashout, $action, $request->validated()));
    }
}

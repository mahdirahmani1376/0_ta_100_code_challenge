<?php

namespace App\Http\Controllers\Admin\ClientCashout;

use App\Actions\Admin\ClientCashout\IndexClientCashoutAction;
use App\Http\Requests\Admin\ClientCashout\IndexClientCashoutRequest;
use App\Http\Resources\Admin\ClientCashout\ClientCashoutResource;

class IndexClientCashoutController
{
    private IndexClientCashoutAction $indexClientCashoutAction;

    public function __construct(IndexClientCashoutAction $indexClientCashoutAction)
    {
        $this->indexClientCashoutAction = $indexClientCashoutAction;
    }

    public function __invoke(IndexClientCashoutRequest $request)
    {
        $clientCashouts = ($this->indexClientCashoutAction)($request->validated());

        return ClientCashoutResource::collection($clientCashouts);
    }
}

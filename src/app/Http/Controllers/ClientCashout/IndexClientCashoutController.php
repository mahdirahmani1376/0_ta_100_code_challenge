<?php

namespace App\Http\Controllers\ClientCashout;

use App\Actions\ClientCashout\IndexClientCashoutAction;
use App\Http\Requests\ClientCashout\IndexClientCashoutRequest;
use App\Http\Resources\ClientCashout\ClientCashoutResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexClientCashoutController
{
    public function __construct(private readonly IndexClientCashoutAction $indexClientCashoutAction)
    {
    }

    /**
     * @param IndexClientCashoutRequest $request
     * @return AnonymousResourceCollection
     */
    public function __invoke(IndexClientCashoutRequest $request)
    {
        $clientCashouts = ($this->indexClientCashoutAction)($request->validated());

        return ClientCashoutResource::collection($clientCashouts);
    }
}

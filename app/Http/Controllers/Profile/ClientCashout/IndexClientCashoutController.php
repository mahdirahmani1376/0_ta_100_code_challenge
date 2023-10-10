<?php

namespace App\Http\Controllers\Profile\ClientCashout;

use App\Actions\Profile\ClientCashout\IndexClientCashoutAction;
use App\Http\Requests\Profile\ClientCashout\IndexClientCashoutRequest;
use App\Http\Resources\Profile\ClientCashout\ClientCashoutResource;
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
        $clientCashouts = ($this->indexClientCashoutAction)(request('client_id'), $request->validated());

        return ClientCashoutResource::collection($clientCashouts);
    }
}

<?php

namespace App\Http\Controllers\Profile\ClientCashout;

use App\Actions\Profile\ClientCashout\IndexClientCashoutAction;
use App\Http\Requests\Profile\ClientCashout\IndexClientCashoutRequest;
use App\Http\Resources\Profile\ClientCashout\ClientCashoutResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexClientCashoutController
{
    private IndexClientCashoutAction $indexClientCashoutAction;

    public function __construct(IndexClientCashoutAction $indexClientCashoutAction)
    {
        $this->indexClientCashoutAction = $indexClientCashoutAction;
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

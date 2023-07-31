<?php

namespace App\Http\Controllers\Admin\ClientCashout;

use App\Http\Resources\Admin\ClientCashout\ClientCashoutResource;
use App\Models\ClientCashout;

class ShowClientCashoutController
{
    /**
     * @param ClientCashout $clientCashout
     * @return ClientCashoutResource
     */
    public function __invoke(ClientCashout $clientCashout)
    {
        return ClientCashoutResource::make($clientCashout);
    }
}

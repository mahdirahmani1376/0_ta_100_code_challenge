<?php

namespace App\Http\Controllers\Admin\ClientCashout;

use App\Http\Resources\Admin\ClientCashout\ClientCashoutResource;
use App\Models\ClientCashout;

class ShowClientCashoutController
{
    public function __invoke(ClientCashout $clientCashout)
    {
        return ClientCashoutResource::make($clientCashout);
    }
}

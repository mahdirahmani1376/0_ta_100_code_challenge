<?php

namespace App\Http\Controllers\ClientCashout;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClientCashout\ClientCashoutResource;
use App\Models\ClientCashout;

class ShowClientCashoutController extends Controller
{
    /**
     * @param ClientCashout $clientCashout
     * @return ClientCashoutResource
     */
    public function __invoke(ClientCashout $clientCashout)
    {
        // TODO implement zarinpal->syncStatusWithZarinpal()
        return ClientCashoutResource::make($clientCashout);
    }
}

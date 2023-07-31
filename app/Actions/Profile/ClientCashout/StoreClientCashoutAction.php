<?php

namespace App\Actions\Profile\ClientCashout;

use App\Models\ClientCashout;
use App\Services\Profile\ClientCashout\StoreClientCashoutService;

class StoreClientCashoutAction
{
    private StoreClientCashoutService $clientCashoutService;

    public function __construct(StoreClientCashoutService $storeClientCashoutService)
    {
        $this->clientCashoutService = $storeClientCashoutService;
    }

    public function __invoke(int $clientId, array $data)
    {
        $data['status'] = ClientCashout::STATUS_PENDING;
        $data['client_id'] = $clientId;

        return ($this->clientCashoutService)($data);
    }
}

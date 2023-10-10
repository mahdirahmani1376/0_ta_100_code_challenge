<?php

namespace App\Actions\Profile\ClientCashout;

use App\Models\ClientCashout;
use App\Services\Profile\ClientCashout\StoreClientCashoutService;

class StoreClientCashoutAction
{
    public function __construct(private readonly StoreClientCashoutService $storeClientCashoutService)
    {
    }

    public function __invoke(int $clientId, array $data)
    {
        $data['status'] = ClientCashout::STATUS_PENDING;
        $data['client_id'] = $clientId;

        return ($this->storeClientCashoutService)($data);
    }
}

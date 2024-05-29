<?php

namespace App\Actions\ClientCashout;

use App\Models\ClientCashout;
use App\Services\ClientCashout\StoreClientCashoutService;

class StoreClientCashoutAction
{
    public function __construct(private readonly StoreClientCashoutService $clientCashoutService)
    {
    }

    public function __invoke(array $data)
    {
        $data['status'] = $data['status'] ?? ClientCashout::STATUS_PENDING;
        if (empty($data['admin_id'])) {
            $data['status'] = ClientCashout::STATUS_PENDING;
        }
        $clientCashout = ($this->clientCashoutService)($data);


        return $clientCashout;
    }
}

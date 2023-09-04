<?php

namespace App\Actions\Admin\ClientCashout;

use App\Models\ClientCashout;
use App\Services\Admin\ClientCashout\StoreClientCashoutService;

class StoreClientCashoutAction
{
    public function __construct(private readonly StoreClientCashoutService $clientCashoutService)
    {
    }

    public function __invoke(array $data)
    {
        $data['status'] = $data['status'] ?? ClientCashout::STATUS_PENDING;

        return ($this->clientCashoutService)($data);
    }
}

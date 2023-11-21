<?php

namespace App\Actions\Profile\ClientCashout;

use App\Models\ClientCashout;
use App\Services\Profile\ClientCashout\StoreClientCashoutService;

class StoreClientCashoutAction
{
    public function __construct(private readonly StoreClientCashoutService $storeClientCashoutService)
    {
    }

    public function __invoke(int $profileId, array $data)
    {
        $data['status'] = ClientCashout::STATUS_PENDING;
        $data['profile_id'] = $profileId;

        return ($this->storeClientCashoutService)($data);
    }
}

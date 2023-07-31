<?php

namespace App\Actions\Profile\ClientCashout;

use App\Models\ClientCashout;
use App\Services\Profile\ClientCashout\UpdateClientCashoutService;

class UpdateClientCashoutAction
{
    private UpdateClientCashoutService $updateClientCashoutService;

    public function __construct(UpdateClientCashoutService $updateClientCashoutService)
    {
        $this->updateClientCashoutService = $updateClientCashoutService;
    }

    public function __invoke(ClientCashout $clientCashout, array $data)
    {
        return ($this->updateClientCashoutService)($clientCashout, $data);
    }
}

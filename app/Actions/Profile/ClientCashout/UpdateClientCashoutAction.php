<?php

namespace App\Actions\Profile\ClientCashout;

use App\Models\ClientCashout;
use App\Services\Profile\ClientCashout\UpdateClientCashoutService;

class UpdateClientCashoutAction
{
    public function __construct(private readonly UpdateClientCashoutService $updateClientCashoutService)
    {
    }

    public function __invoke(ClientCashout $clientCashout, array $data)
    {
        return ($this->updateClientCashoutService)($clientCashout, $data);
    }
}

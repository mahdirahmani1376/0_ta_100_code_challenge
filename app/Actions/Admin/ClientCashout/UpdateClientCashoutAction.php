<?php

namespace App\Actions\Admin\ClientCashout;

use App\Models\ClientCashout;
use App\Services\Admin\ClientCashout\UpdateClientCashoutService;

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

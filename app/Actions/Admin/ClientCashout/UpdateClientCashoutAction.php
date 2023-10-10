<?php

namespace App\Actions\Admin\ClientCashout;

use App\Models\AdminLog;
use App\Models\ClientCashout;
use App\Services\Admin\ClientCashout\UpdateClientCashoutService;

class UpdateClientCashoutAction
{
    public function __construct(private readonly UpdateClientCashoutService $updateClientCashoutService)
    {
    }

    public function __invoke(ClientCashout $clientCashout, array $data)
    {
        $oldState = $clientCashout->toArray();
        $clientCashout = ($this->updateClientCashoutService)($clientCashout, $data);

        admin_log(AdminLog::CREATE_CASHOUT, $clientCashout, $clientCashout->getChanges(), $oldState, $data);

        return $clientCashout;
    }
}

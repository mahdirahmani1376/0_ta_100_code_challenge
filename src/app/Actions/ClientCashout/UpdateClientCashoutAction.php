<?php

namespace App\Actions\ClientCashout;

use App\Exceptions\Http\BadRequestException;
use App\Models\AdminLog;
use App\Models\ClientCashout;
use App\Services\ClientCashout\UpdateClientCashoutService;

class UpdateClientCashoutAction
{
    public function __construct(private readonly UpdateClientCashoutService $updateClientCashoutService)
    {
    }

    public function __invoke(ClientCashout $clientCashout, array $data)
    {
        if (isset($data['profile_id']) && $data['profile_id'] != $clientCashout->profile_id) {
            throw new BadRequestException(__('finance.error.AccessDeniedToCashout'));
        }
        $oldState = $clientCashout->toArray();
        $clientCashout = ($this->updateClientCashoutService)($clientCashout, $data);

        admin_log(AdminLog::CREATE_CASHOUT, $clientCashout, $clientCashout->getChanges(), $oldState, $data);

        return $clientCashout;
    }
}

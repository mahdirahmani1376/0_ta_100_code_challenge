<?php

namespace App\Actions\ClientCashout;

use App\Exceptions\Http\BadRequestException;
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

        $bank_account_id = data_get($data, 'client_bank_account_id');
        if (
            $clientCashout->client_bank_account_id != $bank_account_id && $bank_account_id > 0
            &&
            $clientCashout->status == ClientCashout::STATUS_REJECTED
        ) {
            $data['status'] = ClientCashout::STATUS_PENDING;
        }

        $clientCashout = ($this->updateClientCashoutService)($clientCashout, $data);


        return $clientCashout;
    }
}

<?php

namespace App\Services\Profile\ClientCashout;

use App\Models\ClientCashout;
use App\Repositories\ClientCashout\Interface\ClientCashoutRepositoryInterface;

class UpdateClientCashoutService
{
    public function __construct(private readonly ClientCashoutRepositoryInterface $clientCashoutRepository)
    {
    }

    public function __invoke(ClientCashout $clientCashout, array $data): ClientCashout
    {
        return $this->clientCashoutRepository->update($clientCashout, $data, [
            'client_bank_account_id',
        ]);
    }
}

<?php

namespace App\Services\Profile\ClientCashout;

use App\Models\ClientCashout;
use App\Repositories\ClientCashout\Interface\ClientCashoutRepositoryInterface;

class UpdateClientCashoutService
{
    private ClientCashoutRepositoryInterface $clientCashoutRepository;

    public function __construct(ClientCashoutRepositoryInterface $clientCashoutRepository)
    {
        $this->clientCashoutRepository = $clientCashoutRepository;
    }

    public function __invoke(ClientCashout $clientCashout, array $data): ClientCashout
    {
        return $this->clientCashoutRepository->update($clientCashout, $data, [
            'client_bank_account_id',
        ]);
    }
}

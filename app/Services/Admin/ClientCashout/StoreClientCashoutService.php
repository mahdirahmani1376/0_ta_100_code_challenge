<?php

namespace App\Services\Admin\ClientCashout;

use App\Models\ClientCashout;
use App\Repositories\ClientCashout\Interface\ClientCashoutRepositoryInterface;

class StoreClientCashoutService
{
    private ClientCashoutRepositoryInterface $clientCashoutRepository;

    public function __construct(ClientCashoutRepositoryInterface $clientCashoutRepository)
    {
        $this->clientCashoutRepository = $clientCashoutRepository;
    }

    public function __invoke(array $data): ClientCashout
    {
        return $this->clientCashoutRepository->create($data, [
            'client_id',
            'client_bank_account_id',
            'zarinpal_payout_id',
            'admin_id',
            'amount',
            'admin_note',
            'status',
            'rejected_by_bank',
        ]);
    }
}

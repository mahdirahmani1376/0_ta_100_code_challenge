<?php

namespace App\Services\ClientCashout;

use App\Models\ClientCashout;
use App\Repositories\ClientCashout\Interface\ClientCashoutRepositoryInterface;

class StoreClientCashoutService
{
    public function __construct(private readonly ClientCashoutRepositoryInterface $clientCashoutRepository)
    {
    }

    public function __invoke(array $data): ClientCashout
    {
        return $this->clientCashoutRepository->create($data, [
            'profile_id',
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

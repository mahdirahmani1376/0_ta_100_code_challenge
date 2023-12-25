<?php

namespace App\Services\ClientCashout;

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

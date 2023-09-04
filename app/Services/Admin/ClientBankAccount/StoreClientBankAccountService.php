<?php

namespace App\Services\Admin\ClientBankAccount;

use App\Models\ClientBankAccount;
use App\Repositories\ClientBankAccount\Interface\ClientBankAccountRepositoryInterface;

class StoreClientBankAccountService
{
    public function __construct(private readonly ClientBankAccountRepositoryInterface $clientBankAccountRepository)
    {
    }

    public function __invoke(array $data): ClientBankAccount
    {
        return $this->clientBankAccountRepository->create($data, [
            'status',
            'bank_name',
            'card_number',
            'sheba_number',
            'account_number',
            'client_id',
            'zarinpal_bank_account_id',
            'owner_name',
        ]);
    }
}

<?php

namespace App\Services\Admin\ClientBankAccount;

use App\Models\ClientBankAccount;
use App\Repositories\ClientBankAccount\Interface\ClientBankAccountRepositoryInterface;

class UpdateClientBankAccountService
{
    public function __construct(private readonly ClientBankAccountRepositoryInterface $clientBankAccountRepository)
    {
    }

    public function __invoke(ClientBankAccount $clientBankAccount, array $data): ClientBankAccount
    {
        return $this->clientBankAccountRepository->update($clientBankAccount, $data, [
            'status',
            'bank_name',
            'card_number',
            'sheba_number',
            'account_number',
            'zarinpal_bank_account_id',
            'owner_name',
        ]);
    }
}

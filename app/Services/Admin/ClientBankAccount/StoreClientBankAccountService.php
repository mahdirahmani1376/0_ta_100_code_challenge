<?php

namespace App\Services\Admin\ClientBankAccount;

use App\Models\ClientBankAccount;
use App\Repositories\ClientBankAccount\Interface\ClientBankAccountRepositoryInterface;

class StoreClientBankAccountService
{
    private ClientBankAccountRepositoryInterface $clientBankAccountRepository;

    public function __construct(ClientBankAccountRepositoryInterface $clientBankAccountRepository)
    {
        $this->clientBankAccountRepository = $clientBankAccountRepository;
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

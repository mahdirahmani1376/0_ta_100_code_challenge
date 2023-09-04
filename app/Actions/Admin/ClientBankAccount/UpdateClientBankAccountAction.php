<?php

namespace App\Actions\Admin\ClientBankAccount;

use App\Models\ClientBankAccount;
use App\Services\Admin\ClientBankAccount\UpdateClientBankAccountService;

class UpdateClientBankAccountAction
{
    public function __construct(private readonly UpdateClientBankAccountService $updateClientBankAccountService)
    {
    }

    public function __invoke(ClientBankAccount $clientBankAccount, array $data): ClientBankAccount
    {
        return ($this->updateClientBankAccountService)($clientBankAccount, $data);
    }
}

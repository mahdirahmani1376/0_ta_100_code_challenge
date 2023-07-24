<?php

namespace App\Actions\Admin\ClientBankAccount;

use App\Models\ClientBankAccount;
use App\Services\Admin\ClientBankAccount\UpdateClientBankAccountService;

class UpdateClientBankAccountAction
{
    private UpdateClientBankAccountService $updateClientBankAccountService;

    public function __construct(UpdateClientBankAccountService $updateClientBankAccountService)
    {
        $this->updateClientBankAccountService = $updateClientBankAccountService;
    }

    public function __invoke(ClientBankAccount $clientBankAccount, array $data): ClientBankAccount
    {
        return ($this->updateClientBankAccountService)($clientBankAccount, $data);
    }
}

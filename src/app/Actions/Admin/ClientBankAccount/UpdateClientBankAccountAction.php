<?php

namespace App\Actions\Admin\ClientBankAccount;

use App\Models\AdminLog;
use App\Models\ClientBankAccount;
use App\Services\Admin\ClientBankAccount\UpdateClientBankAccountService;

class UpdateClientBankAccountAction
{
    public function __construct(private readonly UpdateClientBankAccountService $updateClientBankAccountService)
    {
    }

    public function __invoke(ClientBankAccount $clientBankAccount, array $data): ClientBankAccount
    {
        $oldState = $clientBankAccount->toArray();
        $clientBankAccount = ($this->updateClientBankAccountService)($clientBankAccount, $data);

        admin_log(AdminLog::UPDATE_CLIENT_BANK_ACCOUNT, $clientBankAccount, $clientBankAccount->getChanges(), $oldState, $data);

        return $clientBankAccount;
    }
}

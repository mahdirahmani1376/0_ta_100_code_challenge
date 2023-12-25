<?php

namespace App\Actions\ClientBankAccount;

use App\Models\AdminLog;
use App\Models\ClientBankAccount;
use App\Services\ClientBankAccount\UpdateClientBankAccountService;

class UpdateClientBankAccountAction
{
    public function __construct(private readonly UpdateClientBankAccountService $updateClientBankAccountService)
    {
    }

    public function __invoke(ClientBankAccount $clientBankAccount, array $data): ClientBankAccount
    {
        $oldState = $clientBankAccount->toArray();
        if (empty($data['admin_id'])) {
            $data['status'] = ClientBankAccount::STATUS_PENDING;
        }
        $clientBankAccount = ($this->updateClientBankAccountService)($clientBankAccount, $data);

        admin_log(AdminLog::UPDATE_CLIENT_BANK_ACCOUNT, $clientBankAccount, $clientBankAccount->getChanges(), $oldState, $data);

        return $clientBankAccount;
    }
}

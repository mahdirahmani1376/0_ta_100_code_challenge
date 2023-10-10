<?php

namespace App\Actions\Admin\ClientBankAccount;

use App\Models\AdminLog;
use App\Models\ClientBankAccount;
use App\Services\Admin\ClientBankAccount\StoreClientBankAccountService;

class StoreClientBankAccountAction
{
    public function __construct(private readonly StoreClientBankAccountService $storeClientBankAccountService)
    {
    }

    public function __invoke(array $data): ClientBankAccount
    {
        $clientBankAccount = ($this->storeClientBankAccountService)($data);

        admin_log(AdminLog::CREATE_CLIENT_BANK_ACCOUNT, $clientBankAccount, validatedData: $data);

        return $clientBankAccount;
    }
}

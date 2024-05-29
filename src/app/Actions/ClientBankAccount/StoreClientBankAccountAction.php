<?php

namespace App\Actions\ClientBankAccount;

use App\Models\ClientBankAccount;
use App\Services\ClientBankAccount\StoreClientBankAccountService;

class StoreClientBankAccountAction
{
    public function __construct(private readonly StoreClientBankAccountService $storeClientBankAccountService)
    {
    }

    public function __invoke(array $data): ClientBankAccount
    {
        if (empty($data['admin_id'])) {
            $data['status'] = ClientBankAccount::STATUS_PENDING;
        }
        $clientBankAccount = ($this->storeClientBankAccountService)($data);


        return $clientBankAccount;
    }
}

<?php

namespace App\Actions\Admin\ClientBankAccount;

use App\Models\ClientBankAccount;
use App\Services\Admin\ClientBankAccount\StoreClientBankAccountService;

class StoreClientBankAccountAction
{
    public function __construct(private readonly StoreClientBankAccountService $storeClientBankAccountService)
    {
    }

    public function __invoke(array $data): ClientBankAccount
    {
        return ($this->storeClientBankAccountService)($data);
    }
}

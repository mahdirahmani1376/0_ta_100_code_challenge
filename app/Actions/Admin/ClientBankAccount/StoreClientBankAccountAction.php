<?php

namespace App\Actions\Admin\ClientBankAccount;

use App\Models\ClientBankAccount;
use App\Services\Admin\ClientBankAccount\StoreClientBankAccountService;

class StoreClientBankAccountAction
{
    private StoreClientBankAccountService $storeClientBankAccountService;

    public function __construct(StoreClientBankAccountService $storeClientBankAccountService)
    {
        $this->storeClientBankAccountService = $storeClientBankAccountService;
    }

    public function __invoke(array $data): ClientBankAccount
    {
        return ($this->storeClientBankAccountService)($data);
    }
}

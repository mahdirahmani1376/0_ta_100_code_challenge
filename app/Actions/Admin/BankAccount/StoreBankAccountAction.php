<?php

namespace App\Actions\Admin\BankAccount;

use App\Services\Admin\BankAccount\StoreBankAccountService;

class StoreBankAccountAction
{
    private StoreBankAccountService $storeBankAccountService;

    public function __construct(StoreBankAccountService $storeBankAccountService)
    {
        $this->storeBankAccountService = $storeBankAccountService;
    }

    public function __invoke(array $data)
    {
        return ($this->storeBankAccountService)($data);
    }
}

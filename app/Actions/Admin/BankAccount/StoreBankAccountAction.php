<?php

namespace App\Actions\Admin\BankAccount;

use App\Services\Admin\BankAccount\StoreBankAccountService;

class StoreBankAccountAction
{
    public function __construct(private readonly StoreBankAccountService $storeBankAccountService)
    {
    }

    public function __invoke(array $data)
    {
        return ($this->storeBankAccountService)($data);
    }
}

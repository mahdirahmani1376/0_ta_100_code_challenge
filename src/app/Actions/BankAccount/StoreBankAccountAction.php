<?php

namespace App\Actions\BankAccount;

use App\Services\BankAccount\StoreBankAccountService;

class StoreBankAccountAction
{
    public function __construct(private readonly StoreBankAccountService $storeBankAccountService)
    {
    }

    public function __invoke(array $data)
    {
        $bankAccount = ($this->storeBankAccountService)($data);

        return $bankAccount;
    }
}

<?php

namespace App\Actions\BankAccount;

use App\Models\AdminLog;
use App\Services\BankAccount\StoreBankAccountService;

class StoreBankAccountAction
{
    public function __construct(private readonly StoreBankAccountService $storeBankAccountService)
    {
    }

    public function __invoke(array $data)
    {
        $bankAccount = ($this->storeBankAccountService)($data);
        admin_log(AdminLog::CREATE_BANK_ACCOUNT, $bankAccount, oldState: $bankAccount, validatedData: $data);

        return $bankAccount;
    }
}

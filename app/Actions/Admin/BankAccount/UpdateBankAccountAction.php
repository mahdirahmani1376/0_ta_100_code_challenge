<?php

namespace App\Actions\Admin\BankAccount;

use App\Models\BankAccount;
use App\Services\Admin\BankAccount\UpdateBankAccountService;

class UpdateBankAccountAction
{
    public function __construct(private readonly UpdateBankAccountService $updateBankAccountService)
    {
    }

    public function __invoke(BankAccount $bankAccount, array $data)
    {
        return ($this->updateBankAccountService)($bankAccount, $data);
    }
}

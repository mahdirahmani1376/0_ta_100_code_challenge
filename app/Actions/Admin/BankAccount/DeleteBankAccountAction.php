<?php

namespace App\Actions\Admin\BankAccount;

use App\Models\BankAccount;
use App\Services\Admin\BankAccount\DeleteBankAccountService;

class DeleteBankAccountAction
{
    public function __construct(private readonly DeleteBankAccountService $deleteBankAccountService)
    {
    }

    public function __invoke(BankAccount $bankAccount)
    {
        return ($this->deleteBankAccountService)($bankAccount);
    }
}

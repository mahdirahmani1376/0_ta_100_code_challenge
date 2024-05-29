<?php

namespace App\Actions\BankAccount;

use App\Models\BankAccount;
use App\Services\BankAccount\DeleteBankAccountService;

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

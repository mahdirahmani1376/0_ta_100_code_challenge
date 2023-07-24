<?php

namespace App\Actions\Admin\BankAccount;

use App\Models\BankAccount;
use App\Services\Admin\BankAccount\DeleteBankAccountService;

class DeleteBankAccountAction
{
    private DeleteBankAccountService $deleteBankAccountService;

    public function __construct(DeleteBankAccountService $deleteBankAccountService)
    {
        $this->deleteBankAccountService = $deleteBankAccountService;
    }

    public function __invoke(BankAccount $bankAccount)
    {
        return ($this->deleteBankAccountService)($bankAccount);
    }
}

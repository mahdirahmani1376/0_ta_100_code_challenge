<?php

namespace App\Actions\BankAccount;

use App\Models\BankAccount;
use App\Services\BankAccount\UpdateBankAccountService;

class UpdateBankAccountAction
{
    public function __construct(private readonly UpdateBankAccountService $updateBankAccountService)
    {
    }

    public function __invoke(BankAccount $bankAccount, array $data)
    {
        $oldState = $bankAccount->toArray();
        $bankAccount = ($this->updateBankAccountService)($bankAccount, $data);

        return $bankAccount;
    }
}

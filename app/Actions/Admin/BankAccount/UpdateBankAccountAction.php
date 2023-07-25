<?php

namespace App\Actions\Admin\BankAccount;

use App\Models\BankAccount;
use App\Services\Admin\BankAccount\UpdateBankAccountService;

class UpdateBankAccountAction
{
    private UpdateBankAccountService $updateBankAccountService;

    public function __construct(UpdateBankAccountService $updateBankAccountService)
    {
        $this->updateBankAccountService = $updateBankAccountService;
    }

    public function __invoke(BankAccount $bankAccount, array $data)
    {
        return ($this->updateBankAccountService)($bankAccount, $data);
    }
}

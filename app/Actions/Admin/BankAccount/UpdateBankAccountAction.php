<?php

namespace App\Actions\Admin\BankAccount;

use App\Models\AdminLog;
use App\Models\BankAccount;
use App\Services\Admin\BankAccount\UpdateBankAccountService;

class UpdateBankAccountAction
{
    public function __construct(private readonly UpdateBankAccountService $updateBankAccountService)
    {
    }

    public function __invoke(BankAccount $bankAccount, array $data)
    {
        $oldState = $bankAccount->toArray();
        $bankAccount = ($this->updateBankAccountService)($bankAccount, $data);
        admin_log(AdminLog::UPDATE_BANK_ACCOUNT, $bankAccount, $bankAccount->getChanges(), $oldState, $data);

        return $bankAccount;
    }
}

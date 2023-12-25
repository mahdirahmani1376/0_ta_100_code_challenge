<?php

namespace App\Actions\BankAccount;

use App\Models\BankAccount;
use App\Services\BankAccount\IndexBankAccountService;

class IndexBankAccountAction
{
    public function __construct(private readonly IndexBankAccountService $indexBankAccountService)
    {
    }

    public function __invoke(array $data)
    {
        if (!isset($data['admin_id'])) {
            $data['status'] = BankAccount::STATUS_ACTIVE;
        }

        return ($this->indexBankAccountService)($data);
    }
}

<?php

namespace App\Actions\Admin\BankAccount;

use App\Services\Admin\BankAccount\IndexBankAccountService;

class IndexBankAccountAction
{
    public function __construct(private readonly IndexBankAccountService $indexBankAccountService)
    {
    }

    public function __invoke(array $data)
    {
        return ($this->indexBankAccountService)($data);
    }
}

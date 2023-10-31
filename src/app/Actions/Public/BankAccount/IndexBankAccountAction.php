<?php

namespace App\Actions\Public\BankAccount;

use App\Services\BankAccount\IndexBankAccountService;

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

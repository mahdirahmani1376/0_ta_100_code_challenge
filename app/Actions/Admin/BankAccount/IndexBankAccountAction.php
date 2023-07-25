<?php

namespace App\Actions\Admin\BankAccount;

use App\Services\Admin\BankAccount\IndexBankAccountService;

class IndexBankAccountAction
{
    private IndexBankAccountService $indexBankAccountService;

    public function __construct(IndexBankAccountService $indexBankAccountService)
    {
        $this->indexBankAccountService = $indexBankAccountService;
    }

    public function __invoke(array $data)
    {
        return ($this->indexBankAccountService)($data);
    }
}

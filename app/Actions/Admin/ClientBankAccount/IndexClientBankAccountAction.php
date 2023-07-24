<?php

namespace App\Actions\Admin\ClientBankAccount;

use App\Services\Admin\ClientBankAccount\IndexClientBankAccountService;

class IndexClientBankAccountAction
{
    private IndexClientBankAccountService $indexClientBankAccountService;

    public function __construct(IndexClientBankAccountService $indexClientBankAccountService)
    {
        $this->indexClientBankAccountService = $indexClientBankAccountService;
    }

    public function __invoke(array $data)
    {
        return ($this->indexClientBankAccountService)($data);
    }
}

<?php

namespace App\Actions\Admin\ClientBankAccount;

use App\Services\Admin\ClientBankAccount\IndexClientBankAccountService;

class IndexClientBankAccountAction
{
    public function __construct(private readonly IndexClientBankAccountService $indexClientBankAccountService)
    {
    }

    public function __invoke(array $data)
    {
        return ($this->indexClientBankAccountService)($data);
    }
}

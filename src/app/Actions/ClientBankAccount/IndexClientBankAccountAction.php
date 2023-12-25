<?php

namespace App\Actions\ClientBankAccount;

use App\Services\ClientBankAccount\IndexClientBankAccountService;

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

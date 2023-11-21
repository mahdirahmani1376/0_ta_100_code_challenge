<?php

namespace App\Actions\Profile\ClientBankAccount;

use App\Services\Profile\ClientBankAccount\IndexClientBankAccountService;

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

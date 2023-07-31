<?php

namespace App\Actions\Profile\ClientBankAccount;

use App\Services\Profile\ClientBankAccount\IndexClientBankAccountService;

class IndexClientBankAccountAction
{
    private IndexClientBankAccountService $indexClientBankAccountService;

    public function __construct(IndexClientBankAccountService $indexClientBankAccountService)
    {
        $this->indexClientBankAccountService = $indexClientBankAccountService;
    }

    public function __invoke(int $clientId, array $data)
    {
        return ($this->indexClientBankAccountService)($clientId, $data);
    }
}

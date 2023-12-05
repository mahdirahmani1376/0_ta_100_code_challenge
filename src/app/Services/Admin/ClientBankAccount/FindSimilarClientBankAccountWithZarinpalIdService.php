<?php

namespace App\Services\Admin\ClientBankAccount;

use App\Models\ClientBankAccount;
use App\Repositories\ClientBankAccount\Interface\ClientBankAccountRepositoryInterface;

class FindSimilarClientBankAccountWithZarinpalIdService
{
    public function __construct(private readonly ClientBankAccountRepositoryInterface $clientBankAccountRepository)
    {
    }

    public function __invoke(ClientBankAccount $clientBankAccount): ?ClientBankAccount
    {
        return $this->clientBankAccountRepository->findSimilarWithZarinpalId($clientBankAccount);
    }
}

<?php

namespace App\Services\BankAccount;

use App\Models\BankAccount;
use App\Repositories\BankAccount\Interface\BankAccountRepositoryInterface;

class DeleteBankAccountService
{
    public function __construct(private readonly BankAccountRepositoryInterface $bankAccountRepository)
    {
    }

    public function __invoke(BankAccount $bankAccount)
    {
        return $this->bankAccountRepository->delete($bankAccount);
    }
}

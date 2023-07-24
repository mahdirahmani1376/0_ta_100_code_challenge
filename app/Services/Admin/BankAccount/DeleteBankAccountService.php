<?php

namespace App\Services\Admin\BankAccount;

use App\Models\BankAccount;
use App\Repositories\BankAccount\Interface\BankAccountRepositoryInterface;

class DeleteBankAccountService
{
    private BankAccountRepositoryInterface $bankAccountRepository;

    public function __construct(BankAccountRepositoryInterface $bankAccountRepository)
    {
        $this->bankAccountRepository = $bankAccountRepository;
    }

    public function __invoke(BankAccount $bankAccount)
    {
        return $this->bankAccountRepository->delete($bankAccount);
    }
}

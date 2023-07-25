<?php

namespace App\Services\Admin\BankAccount;

use App\Models\BankAccount;
use App\Repositories\BankAccount\Interface\BankAccountRepositoryInterface;

class UpdateBankAccountService
{
    private BankAccountRepositoryInterface $bankAccountRepository;

    public function __construct(BankAccountRepositoryInterface $bankAccountRepository)
    {
        $this->bankAccountRepository = $bankAccountRepository;
    }

    public function __invoke(BankAccount $bankAccount, array $data): BankAccount
    {
        return $this->bankAccountRepository->update($bankAccount, $data, [
            'sheba_number',
            'account_number',
            'card_number',
            'title',
            'display_order',
            'rahkaran_id',
        ]);
    }
}

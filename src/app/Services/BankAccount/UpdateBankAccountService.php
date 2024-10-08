<?php

namespace App\Services\BankAccount;

use App\Models\BankAccount;
use App\Repositories\BankAccount\Interface\BankAccountRepositoryInterface;

class UpdateBankAccountService
{
    public function __construct(private readonly BankAccountRepositoryInterface $bankAccountRepository)
    {
    }

    public function __invoke(BankAccount $bankAccount, array $data): BankAccount
    {
        $data['sheba_number'] = normalise_sheba_number($data['sheba_number']);

        return $this->bankAccountRepository->update($bankAccount, $data, [
            'sheba_number',
            'account_number',
            'card_number',
            'title',
            'order',
            'rahkaran_id',
            'status',
        ]);
    }
}

<?php

namespace App\Services\Admin\BankAccount;

use App\Models\BankAccount;
use App\Repositories\BankAccount\Interface\BankAccountRepositoryInterface;

class StoreBankAccountService
{
    public function __construct(private readonly BankAccountRepositoryInterface $bankAccountRepository)
    {
    }

    public function __invoke(array $data): BankAccount
    {
        return $this->bankAccountRepository->create($data, [
            'sheba_number',
            'account_number',
            'card_number',
            'title',
            'display_order',
            'rahkaran_id',
            'status',
        ]);
    }
}

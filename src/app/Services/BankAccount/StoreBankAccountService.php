<?php

namespace App\Services\BankAccount;

use App\Models\BankAccount;
use App\Repositories\BankAccount\Interface\BankAccountRepositoryInterface;

class StoreBankAccountService
{
    public function __construct(private readonly BankAccountRepositoryInterface $bankAccountRepository)
    {
    }

    public function __invoke(array $data): BankAccount
    {
        $data['sheba_number'] = normalise_sheba_number($data['sheba_number']);

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

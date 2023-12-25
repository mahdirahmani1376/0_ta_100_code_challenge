<?php

namespace App\Services\ClientBankAccount;

use App\Models\ClientBankAccount;
use App\Repositories\ClientBankAccount\Interface\ClientBankAccountRepositoryInterface;
use Illuminate\Support\Str;

class StoreClientBankAccountService
{
    public function __construct(private readonly ClientBankAccountRepositoryInterface $clientBankAccountRepository)
    {
    }

    public function __invoke(array $data): ClientBankAccount
    {
        $data['sheba_number'] = Str::start($data['sheba_number'], 'IR');

        return $this->clientBankAccountRepository->create($data, [
            'status',
            'bank_name',
            'card_number',
            'sheba_number',
            'account_number',
            'profile_id',
            'zarinpal_bank_account_id',
            'owner_name',
        ]);
    }
}

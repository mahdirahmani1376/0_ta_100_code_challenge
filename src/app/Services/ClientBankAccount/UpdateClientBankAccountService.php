<?php

namespace App\Services\ClientBankAccount;

use App\Models\ClientBankAccount;
use App\Repositories\ClientBankAccount\Interface\ClientBankAccountRepositoryInterface;
use Illuminate\Support\Str;

class UpdateClientBankAccountService
{
    public function __construct(private readonly ClientBankAccountRepositoryInterface $clientBankAccountRepository)
    {
    }

    public function __invoke(ClientBankAccount $clientBankAccount, array $data): ClientBankAccount
    {
        if (isset($data['sheba_number'])) {
            $data['sheba_number'] = normalise_sheba_number($data['sheba_number']);
        }

        return $this->clientBankAccountRepository->update($clientBankAccount, $data, [
            'status',
            'bank_name',
            'card_number',
            'sheba_number',
            'account_number',
            'zarinpal_bank_account_id',
            'owner_name',
        ]);
    }
}

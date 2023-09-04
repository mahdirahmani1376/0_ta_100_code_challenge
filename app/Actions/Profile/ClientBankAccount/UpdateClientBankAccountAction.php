<?php

namespace App\Actions\Profile\ClientBankAccount;

use App\Models\ClientBankAccount;
use App\Services\Profile\ClientBankAccount\UpdateClientBankAccountService;
use Illuminate\Support\Str;

class UpdateClientBankAccountAction
{
    public function __construct(private readonly UpdateClientBankAccountService $updateClientBankAccountService)
    {
    }

    public function __invoke(ClientBankAccount $clientBankAccount, array $data): ClientBankAccount
    {
        // TODO LOG
        // If user is updating its ClientBankAccount record status must change back to "pending"
        $data['status'] = ClientBankAccount::STATUS_PENDING;
        if (!empty($data['sheba_number'])) {
            $data['sheba_number'] = Str::upper($data['sheba_number']);
        }

        return ($this->updateClientBankAccountService)($clientBankAccount, $data);
    }
}

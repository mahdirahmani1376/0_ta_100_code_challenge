<?php

namespace App\Actions\Profile\ClientBankAccount;

use App\Models\ClientBankAccount;
use App\Services\Profile\ClientBankAccount\StoreClientBankAccountService;
use Illuminate\Support\Str;

class StoreClientBankAccountAction
{
    private StoreClientBankAccountService $storeClientBankAccountService;

    public function __construct(StoreClientBankAccountService $storeClientBankAccountService)
    {
        $this->storeClientBankAccountService = $storeClientBankAccountService;
    }

    public function __invoke(array $data): ClientBankAccount
    {
        $data['status'] = ClientBankAccount::STATUS_PENDING;
        if (!empty($data['sheba_number'])){
            $data['sheba_number'] = Str::upper($data['sheba_number']);
        }

        return ($this->storeClientBankAccountService)($data);
    }
}

<?php

namespace App\Http\Controllers\Profile\ClientBankAccount;

use App\Actions\Profile\ClientBankAccount\StoreClientBankAccountAction;
use App\Http\Requests\Profile\ClientBankAccount\StoreClientBankAccountRequest;
use App\Http\Resources\Profile\ClientBankAccount\ClientBankAccountResource;

class StoreClientBankAccountController
{
    private StoreClientBankAccountAction $storeClientBankAccountAction;

    public function __construct(StoreClientBankAccountAction $storeClientBankAccountAction)
    {
        $this->storeClientBankAccountAction = $storeClientBankAccountAction;
    }

    public function __invoke(StoreClientBankAccountRequest $request)
    {
        $clientBankAccount = ($this->storeClientBankAccountAction)($request->validated());

        return ClientBankAccountResource::make($clientBankAccount);
    }
}

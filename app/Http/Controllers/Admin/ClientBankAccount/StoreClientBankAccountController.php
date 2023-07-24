<?php

namespace App\Http\Controllers\Admin\ClientBankAccount;

use App\Actions\Admin\ClientBankAccount\StoreClientBankAccountAction;
use App\Http\Requests\Admin\ClientBankAccount\StoreClientBankAccountRequest;
use App\Http\Resources\Admin\ClientBankAccount\ClientBankAccountResource;

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

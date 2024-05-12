<?php

namespace App\Http\Controllers\ClientBankAccount;

use App\Actions\ClientBankAccount\StoreClientBankAccountAction;
use App\Http\Requests\ClientBankAccount\StoreClientBankAccountRequest;
use App\Http\Resources\ClientBankAccount\ClientBankAccountResource;

class StoreClientBankAccountController
{
    public function __construct(private readonly StoreClientBankAccountAction $storeClientBankAccountAction)
    {
    }

    /**
     * @param StoreClientBankAccountRequest $request
     * @return ClientBankAccountResource
     */
    public function __invoke(StoreClientBankAccountRequest $request)
    {
        $clientBankAccount = ($this->storeClientBankAccountAction)($request->validated());

        return ClientBankAccountResource::make($clientBankAccount);
    }
}

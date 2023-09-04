<?php

namespace App\Http\Controllers\Profile\ClientBankAccount;

use App\Actions\Profile\ClientBankAccount\StoreClientBankAccountAction;
use App\Http\Requests\Profile\ClientBankAccount\StoreClientBankAccountRequest;
use App\Http\Resources\Profile\ClientBankAccount\ClientBankAccountResource;

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

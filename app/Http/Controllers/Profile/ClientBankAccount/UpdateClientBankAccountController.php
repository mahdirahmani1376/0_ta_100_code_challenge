<?php

namespace App\Http\Controllers\Profile\ClientBankAccount;

use App\Actions\Profile\ClientBankAccount\UpdateClientBankAccountAction;
use App\Http\Requests\Profile\ClientBankAccount\UpdateClientBankAccountRequest;
use App\Http\Resources\Profile\ClientBankAccount\ClientBankAccountResource;
use App\Models\ClientBankAccount;

class UpdateClientBankAccountController
{
    public function __construct(private readonly UpdateClientBankAccountAction $updateClientBankAccountAction)
    {
    }

    /**
     * @param ClientBankAccount $clientBankAccount
     * @param UpdateClientBankAccountRequest $request
     * @return ClientBankAccountResource
     */
    public function __invoke(ClientBankAccount $clientBankAccount, UpdateClientBankAccountRequest $request)
    {
        $clientBankAccount = ($this->updateClientBankAccountAction)($clientBankAccount, $request->validated());

        return ClientBankAccountResource::make($clientBankAccount);
    }
}

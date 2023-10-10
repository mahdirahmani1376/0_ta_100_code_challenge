<?php

namespace App\Http\Controllers\Admin\ClientBankAccount;

use App\Actions\Admin\ClientBankAccount\UpdateClientBankAccountAction;
use App\Http\Requests\Admin\ClientBankAccount\UpdateClientBankAccountRequest;
use App\Http\Resources\Admin\ClientBankAccount\ClientBankAccountResource;
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

<?php

namespace App\Http\Controllers\Admin\ClientBankAccount;

use App\Actions\Admin\ClientBankAccount\UpdateClientBankAccountAction;
use App\Http\Requests\Admin\ClientBankAccount\UpdateClientBankAccountRequest;
use App\Http\Resources\Admin\ClientBankAccount\ClientBankAccountResource;
use App\Models\ClientBankAccount;

class UpdateClientBankAccountController
{
    private UpdateClientBankAccountAction $updateClientBankAccountAction;

    public function __construct(UpdateClientBankAccountAction $updateClientBankAccountAction)
    {
        $this->updateClientBankAccountAction = $updateClientBankAccountAction;
    }

    public function __invoke(ClientBankAccount $clientBankAccount, UpdateClientBankAccountRequest $request)
    {
        $clientBankAccount = ($this->updateClientBankAccountAction)($clientBankAccount, $request->validated());

        return ClientBankAccountResource::make($clientBankAccount);
    }
}

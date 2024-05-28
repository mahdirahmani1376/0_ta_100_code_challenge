<?php

namespace App\Http\Controllers\ClientBankAccount;

use App\Actions\ClientBankAccount\UpdateClientBankAccountAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClientBankAccount\UpdateClientBankAccountRequest;
use App\Http\Resources\ClientBankAccount\ClientBankAccountResource;
use App\Models\ClientBankAccount;

class UpdateClientBankAccountController extends Controller
{
    public function __construct(private readonly UpdateClientBankAccountAction $updateClientBankAccountAction)
    {
        parent::__construct();
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

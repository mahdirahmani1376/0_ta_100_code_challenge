<?php

namespace App\Http\Controllers\ClientBankAccount;

use App\Actions\ClientBankAccount\StoreClientBankAccountAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClientBankAccount\StoreClientBankAccountRequest;
use App\Http\Resources\ClientBankAccount\ClientBankAccountResource;

class StoreClientBankAccountController extends Controller
{
    public function __construct(private readonly StoreClientBankAccountAction $storeClientBankAccountAction)
    {
        parent::__construct();
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

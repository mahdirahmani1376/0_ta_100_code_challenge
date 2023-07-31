<?php

namespace App\Http\Controllers\Profile\ClientBankAccount;

use App\Actions\Profile\ClientBankAccount\IndexClientBankAccountAction;
use App\Http\Requests\Profile\ClientBankAccount\IndexClientBankAccountRequest;
use App\Http\Resources\Profile\ClientBankAccount\ClientBankAccountResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexClientBankAccountController
{
    private IndexClientBankAccountAction $indexClientBankAccountAction;

    public function __construct(IndexClientBankAccountAction $indexClientBankAccountAction)
    {
        $this->indexClientBankAccountAction = $indexClientBankAccountAction;
    }

    /**
     * @param IndexClientBankAccountRequest $request
     * @return AnonymousResourceCollection
     */
    public function __invoke(IndexClientBankAccountRequest $request)
    {
        $clientBankAccounts = ($this->indexClientBankAccountAction)(request('client_id'), $request->validated());

        return ClientBankAccountResource::collection($clientBankAccounts);
    }
}

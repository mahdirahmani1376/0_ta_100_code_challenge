<?php

namespace App\Http\Controllers\Admin\ClientBankAccount;

use App\Actions\Admin\ClientBankAccount\IndexClientBankAccountAction;
use App\Http\Requests\Admin\ClientBankAccount\IndexClientBankAccountRequest;
use App\Http\Resources\Admin\ClientBankAccount\ClientBankAccountResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexClientBankAccountController
{
    public function __construct(private readonly IndexClientBankAccountAction $indexClientBankAccountAction)
    {
    }

    /**
     * @param IndexClientBankAccountRequest $request
     * @return AnonymousResourceCollection
     */
    public function __invoke(IndexClientBankAccountRequest $request)
    {
        $clientBankAccounts = ($this->indexClientBankAccountAction)($request->validated());

        return ClientBankAccountResource::collection($clientBankAccounts);
    }
}

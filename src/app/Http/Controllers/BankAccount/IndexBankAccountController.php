<?php

namespace App\Http\Controllers\BankAccount;

use App\Actions\BankAccount\IndexBankAccountAction;
use App\Http\Requests\BankAccount\IndexBankAccountRequest;
use App\Http\Resources\BankAccount\BankAccountResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexBankAccountController
{
    public function __construct(private readonly IndexBankAccountAction $indexBankAccountAction)
    {
    }

    /**
     * @param IndexBankAccountRequest $request
     * @return AnonymousResourceCollection
     */
    public function __invoke(IndexBankAccountRequest $request)
    {
        $bankAccounts = ($this->indexBankAccountAction)($request->validated());

        return BankAccountResource::collection($bankAccounts);
    }
}

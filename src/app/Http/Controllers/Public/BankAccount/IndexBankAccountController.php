<?php

namespace App\Http\Controllers\Public\BankAccount;

use App\Actions\Public\BankAccount\IndexBankAccountAction;
use App\Http\Requests\Public\BankAccount\IndexBankAccountRequest;
use App\Http\Resources\Public\BankAccount\BankAccountResource;

class IndexBankAccountController
{
    public function __construct(private readonly IndexBankAccountAction $indexBankAccountAction)
    {
    }

    public function __invoke(IndexBankAccountRequest $request)
    {
        return BankAccountResource::collection(($this->indexBankAccountAction)($request->validated()));
    }
}

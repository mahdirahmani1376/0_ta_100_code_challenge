<?php

namespace App\Http\Controllers\Admin\BankAccount;

use App\Actions\Admin\BankAccount\IndexBankAccountAction;
use App\Http\Requests\Admin\BankAccount\IndexBankAccountRequest;
use App\Http\Resources\Admin\BankAccount\BankAccountResource;

class IndexBankAccountController
{
    private IndexBankAccountAction $indexBankAccountAction;

    public function __construct(IndexBankAccountAction $indexBankAccountAction)
    {
        $this->indexBankAccountAction = $indexBankAccountAction;
    }

    public function __invoke(IndexBankAccountRequest $request)
    {
        $bankAccounts = ($this->indexBankAccountAction)($request->validated());

        return BankAccountResource::collection($bankAccounts);
    }
}

<?php

namespace App\Http\Controllers\Admin\BankAccount;

use App\Actions\Admin\BankAccount\IndexBankAccountAction;
use App\Http\Requests\Admin\BankAccount\IndexBankAccountRequest;
use App\Http\Resources\Admin\BankAccount\BankAccountResource;
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

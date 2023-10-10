<?php

namespace App\Http\Controllers\Admin\BankAccount;

use App\Actions\Admin\BankAccount\StoreBankAccountAction;
use App\Http\Requests\Admin\BankAccount\StoreBankAccountRequest;
use App\Http\Resources\Admin\BankAccount\BankAccountResource;

class StoreBankAccountController
{
    public function __construct(private readonly StoreBankAccountAction $storeBankAccountAction)
    {
    }

    /**
     * @param StoreBankAccountRequest $request
     * @return BankAccountResource
     */
    public function __invoke(StoreBankAccountRequest $request)
    {
        $bankAccount = ($this->storeBankAccountAction)($request->validated());

        return BankAccountResource::make($bankAccount);
    }
}

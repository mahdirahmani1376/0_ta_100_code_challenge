<?php

namespace App\Http\Controllers\BankAccount;

use App\Actions\BankAccount\StoreBankAccountAction;
use App\Http\Requests\BankAccount\StoreBankAccountRequest;
use App\Http\Resources\BankAccount\BankAccountResource;

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

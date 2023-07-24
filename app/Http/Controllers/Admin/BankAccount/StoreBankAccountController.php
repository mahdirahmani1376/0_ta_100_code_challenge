<?php

namespace App\Http\Controllers\Admin\BankAccount;

use App\Actions\Admin\BankAccount\StoreBankAccountAction;
use App\Http\Requests\Admin\BankAccount\StoreBankAccountRequest;
use App\Http\Resources\Admin\BankAccount\BankAccountResource;

class StoreBankAccountController
{
    private StoreBankAccountAction $storeBankAccountAction;

    public function __construct(StoreBankAccountAction $storeBankAccountAction)
    {
        $this->storeBankAccountAction = $storeBankAccountAction;
    }

    public function __invoke(StoreBankAccountRequest $request)
    {
        $bankAccount = ($this->storeBankAccountAction)($request->validated());

        return BankAccountResource::make($bankAccount);
    }
}

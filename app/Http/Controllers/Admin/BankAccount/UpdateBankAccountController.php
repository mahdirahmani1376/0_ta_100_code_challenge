<?php

namespace App\Http\Controllers\Admin\BankAccount;

use App\Actions\Admin\BankAccount\UpdateBankAccountAction;
use App\Http\Requests\Admin\BankAccount\StoreBankAccountRequest;
use App\Http\Requests\Admin\BankAccount\UpdateBankAccountRequest;
use App\Http\Resources\Admin\BankAccount\BankAccountResource;
use App\Models\BankAccount;

class UpdateBankAccountController
{
    public function __construct(private readonly UpdateBankAccountAction $updateBankAccountAction)
    {
    }

    /**
     * @param BankAccount $bankAccount
     * @param UpdateBankAccountRequest $request
     * @return BankAccountResource
     */
    public function __invoke(BankAccount $bankAccount, UpdateBankAccountRequest $request)
    {
        $bankAccount = ($this->updateBankAccountAction)($bankAccount, $request->validated());

        return BankAccountResource::make($bankAccount);
    }
}

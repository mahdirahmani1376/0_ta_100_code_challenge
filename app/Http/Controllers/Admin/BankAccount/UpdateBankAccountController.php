<?php

namespace App\Http\Controllers\Admin\BankAccount;

use App\Actions\Admin\BankAccount\UpdateBankAccountAction;
use App\Http\Requests\Admin\BankAccount\StoreBankAccountRequest;
use App\Http\Resources\Admin\BankAccount\BankAccountResource;
use App\Models\BankAccount;

class UpdateBankAccountController
{
    private UpdateBankAccountAction $updateBankAccountAction;

    public function __construct(UpdateBankAccountAction $updateBankAccountAction)
    {
        $this->updateBankAccountAction = $updateBankAccountAction;
    }

    public function __invoke(BankAccount $bankAccount, StoreBankAccountRequest $request)
    {
        $bankAccount = ($this->updateBankAccountAction)($bankAccount, $request->validated());

        return BankAccountResource::make($bankAccount);
    }
}

<?php

namespace App\Http\Controllers\Admin\BankAccount;

use App\Actions\Admin\BankAccount\UpdateBankAccountAction;
use App\Http\Requests\Admin\BankAccount\StoreBankAccountRequest;
use App\Http\Resources\Admin\BankAccount\BankAccountResource;
use App\Models\BankAccount;

class ShowBankAccountController
{
    /**
     * @param BankAccount $bankAccount
     * @return BankAccountResource
     */
    public function __invoke(BankAccount $bankAccount)
    {
        return BankAccountResource::make($bankAccount);
    }
}

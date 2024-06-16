<?php

namespace App\Http\Controllers\BankAccount;

use App\Http\Controllers\Controller;
use App\Http\Resources\BankAccount\BankAccountResource;
use App\Models\BankAccount;

class ShowBankAccountController extends Controller
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

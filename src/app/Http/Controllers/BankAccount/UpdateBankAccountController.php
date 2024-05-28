<?php

namespace App\Http\Controllers\BankAccount;

use App\Actions\BankAccount\UpdateBankAccountAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\BankAccount\UpdateBankAccountRequest;
use App\Http\Resources\BankAccount\BankAccountResource;
use App\Models\BankAccount;

class UpdateBankAccountController extends Controller
{
    public function __construct(private readonly UpdateBankAccountAction $updateBankAccountAction)
    {
        parent::__construct();
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

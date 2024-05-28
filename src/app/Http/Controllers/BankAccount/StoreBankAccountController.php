<?php

namespace App\Http\Controllers\BankAccount;

use App\Actions\BankAccount\StoreBankAccountAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\BankAccount\StoreBankAccountRequest;
use App\Http\Resources\BankAccount\BankAccountResource;

class StoreBankAccountController extends Controller
{
    public function __construct(private readonly StoreBankAccountAction $storeBankAccountAction)
    {
        parent::__construct();
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

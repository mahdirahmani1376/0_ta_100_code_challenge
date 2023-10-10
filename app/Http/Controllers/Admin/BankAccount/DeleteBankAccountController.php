<?php

namespace App\Http\Controllers\Admin\BankAccount;

use App\Actions\Admin\BankAccount\DeleteBankAccountAction;
use App\Models\BankAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class DeleteBankAccountController
{
    public function __construct(private readonly DeleteBankAccountAction $deleteBankAccountAction)
    {
    }

    /**
     * @param BankAccount $bankAccount
     * @return JsonResponse
     */
    public function __invoke(BankAccount $bankAccount)
    {
        ($this->deleteBankAccountAction)($bankAccount);

        return response()->json([], Response::HTTP_ACCEPTED);
    }
}

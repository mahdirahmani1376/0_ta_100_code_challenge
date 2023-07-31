<?php

namespace App\Http\Controllers\Admin\BankAccount;

use App\Actions\Admin\BankAccount\DeleteBankAccountAction;
use App\Models\BankAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class DeleteBankAccountController
{
    private DeleteBankAccountAction $deleteBankAccountAction;

    public function __construct(DeleteBankAccountAction $deleteBankAccountAction)
    {
        $this->deleteBankAccountAction = $deleteBankAccountAction;
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

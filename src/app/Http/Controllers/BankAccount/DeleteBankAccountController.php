<?php

namespace App\Http\Controllers\BankAccount;

use App\Actions\BankAccount\DeleteBankAccountAction;
use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class DeleteBankAccountController extends Controller
{
    public function __construct(private readonly DeleteBankAccountAction $deleteBankAccountAction)
    {
        parent::__construct();
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

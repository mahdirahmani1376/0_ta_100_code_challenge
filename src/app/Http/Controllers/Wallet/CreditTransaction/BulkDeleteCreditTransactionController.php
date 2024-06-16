<?php

namespace App\Http\Controllers\Wallet\CreditTransaction;

use App\Actions\Wallet\CreditTransaction\BulkDeleteCreditTransactionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Wallet\CreditTransaction\BulkDeleteCreditTransactionRequest;

class BulkDeleteCreditTransactionController extends Controller
{
    public function __construct(private readonly BulkDeleteCreditTransactionAction $bulkDeleteCreditTransactionAction)
    {
        parent::__construct();
    }

    public function __invoke(BulkDeleteCreditTransactionRequest $request)
    {
        $result = ($this->bulkDeleteCreditTransactionAction)($request->validated());

        return response()->json(['data' => $result]);
    }
}

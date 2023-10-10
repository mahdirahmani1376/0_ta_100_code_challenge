<?php

namespace App\Http\Controllers\Internal\Cloud\Wallet;

use App\Actions\Internal\Cloud\Wallet\DeleteBulkCreditTransactionAction;
use App\Http\Requests\Internal\Cloud\Wallet\DeleteBulkCreditTransactionRequest;
use Illuminate\Http\Response;

class DeleteBulkCreditTransactionController
{
    public function __construct(private readonly DeleteBulkCreditTransactionAction $deleteBulkCreditTransactionAction)
    {
    }

    public function __invoke(DeleteBulkCreditTransactionRequest $request)
    {
        return response()->json([
            'count' => ($this->deleteBulkCreditTransactionAction)($request->validated())
        ], Response::HTTP_ACCEPTED);
    }
}

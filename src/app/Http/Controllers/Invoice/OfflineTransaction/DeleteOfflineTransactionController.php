<?php

namespace App\Http\Controllers\Invoice\OfflineTransaction;

use App\Actions\Invoice\OfflineTransaction\DeleteOfflineTransactionAction;
use App\Http\Requests\Invoice\OfflineTransaction\DeleteOfflineTransactionRequest;
use App\Models\OfflineTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class DeleteOfflineTransactionController
{
    public function __construct(private readonly DeleteOfflineTransactionAction $deleteOfflineTransactionAction)
    {
    }

    /**
     * @param OfflineTransaction $offlineTransaction
     * @return JsonResponse
     */
    public function __invoke(OfflineTransaction $offlineTransaction, DeleteOfflineTransactionRequest $request)
    {
        ($this->deleteOfflineTransactionAction)($offlineTransaction, $request->validated());

        return response()->json([], Response::HTTP_ACCEPTED);
    }
}

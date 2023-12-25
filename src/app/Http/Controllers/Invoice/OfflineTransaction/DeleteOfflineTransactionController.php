<?php

namespace App\Http\Controllers\Invoice\OfflineTransaction;

use App\Actions\Invoice\OfflineTransaction\DeleteOfflineTransactionAction;
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
    public function __invoke(OfflineTransaction $offlineTransaction)
    {
        ($this->deleteOfflineTransactionAction)($offlineTransaction);

        return response()->json([], Response::HTTP_ACCEPTED);
    }
}

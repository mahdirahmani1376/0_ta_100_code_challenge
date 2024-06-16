<?php

namespace App\Http\Controllers\Invoice\OfflineTransaction;

use App\Actions\Invoice\OfflineTransaction\DeleteOfflineTransactionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\OfflineTransaction\DeleteOfflineTransactionRequest;
use App\Models\OfflineTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class DeleteOfflineTransactionController extends Controller
{
    public function __construct(private readonly DeleteOfflineTransactionAction $deleteOfflineTransactionAction)
    {
        parent::__construct();
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

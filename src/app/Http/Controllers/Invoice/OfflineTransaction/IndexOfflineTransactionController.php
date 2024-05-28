<?php

namespace App\Http\Controllers\Invoice\OfflineTransaction;

use App\Actions\Invoice\OfflineTransaction\IndexOfflineTransactionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\OfflineTransaction\IndexOfflineTransactionRequest;
use App\Http\Resources\Invoice\OfflineTransaction\OfflineTransactionResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexOfflineTransactionController extends Controller
{
    public function __construct(private readonly IndexOfflineTransactionAction $indexOfflineTransactionAction)
    {
        parent::__construct();
    }

    /**
     * @param IndexOfflineTransactionRequest $request
     * @return AnonymousResourceCollection
     */
    public function __invoke(IndexOfflineTransactionRequest $request)
    {
        $offlineTransactions = ($this->indexOfflineTransactionAction)($request->validated());

        return OfflineTransactionResource::collection($offlineTransactions);
    }
}

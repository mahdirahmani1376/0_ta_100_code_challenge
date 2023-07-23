<?php

namespace App\Http\Controllers\Admin\OfflineTransaction;

use App\Actions\Admin\OfflineTransaction\UpdateOfflineTransactionAction;
use App\Http\Requests\Admin\OfflineTransaction\UpdateOfflineTransactionRequest;
use App\Http\Resources\Admin\OfflineTransaction\OfflineTransactionResource;
use App\Models\OfflineTransaction;
use Illuminate\Http\Response;

class UpdateOfflineTransactionController
{

    private UpdateOfflineTransactionAction $updateOfflineTransactionAction;

    public function __construct(UpdateOfflineTransactionAction $updateOfflineTransactionAction)
    {
        $this->updateOfflineTransactionAction = $updateOfflineTransactionAction;
    }

    /**
     * @throws \Exception
     */
    public function __invoke(OfflineTransaction $offlineTransaction, UpdateOfflineTransactionRequest $request)
    {
        $offlineTransaction = ($this->updateOfflineTransactionAction)($offlineTransaction, $request->validated());

        return OfflineTransactionResource::make($offlineTransaction);
    }
}

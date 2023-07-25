<?php

namespace App\Http\Controllers\Admin\OfflineTransaction;

use App\Actions\Admin\OfflineTransaction\DeleteOfflineTransactionAction;
use App\Models\OfflineTransaction;
use Illuminate\Http\Response;

class DeleteOfflineTransactionController
{
    private DeleteOfflineTransactionAction $deleteOfflineTransactionAction;

    public function __construct(DeleteOfflineTransactionAction $deleteOfflineTransactionAction)
    {
        $this->deleteOfflineTransactionAction = $deleteOfflineTransactionAction;
    }

    public function __invoke(OfflineTransaction $offlineTransaction)
    {
        ($this->deleteOfflineTransactionAction)($offlineTransaction);

        return response()->json([], Response::HTTP_ACCEPTED);
    }
}

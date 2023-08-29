<?php

namespace App\Http\Controllers\Profile\Invoice;

use App\Actions\Profile\Invoice\DeleteOfflineTransactionAction;
use App\Exceptions\Http\BadRequestException;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Models\OfflineTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class DeleteOfflineTransactionController
{
    private DeleteOfflineTransactionAction $deleteOfflineTransactionAction;

    public function __construct(DeleteOfflineTransactionAction $deleteOfflineTransactionAction)
    {
        $this->deleteOfflineTransactionAction = $deleteOfflineTransactionAction;
    }

    /**
     * @param OfflineTransaction $offlineTransaction
     * @return JsonResponse
     * @throws InvoiceLockedAndAlreadyImportedToRahkaranException
     * @throws BadRequestException
     */
    public function __invoke(OfflineTransaction $offlineTransaction)
    {
        ($this->deleteOfflineTransactionAction)($offlineTransaction);

        return response()->json([], Response::HTTP_ACCEPTED);
    }
}

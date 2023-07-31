<?php

namespace App\Http\Controllers\Profile\Invoice;

use App\Actions\Profile\Invoice\DeleteOfflineTransactionAction;
use App\Exceptions\Http\BadRequestException;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Models\Invoice;
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
     * @param Invoice $invoice
     * @param OfflineTransaction $offlineTransaction
     * @return JsonResponse
     * @throws InvoiceLockedAndAlreadyImportedToRahkaranException
     * @throws BadRequestException
     */
    public function __invoke(Invoice $invoice, OfflineTransaction $offlineTransaction)
    {
        ($this->deleteOfflineTransactionAction)($invoice, $offlineTransaction);

        return response()->json([], Response::HTTP_ACCEPTED);
    }
}

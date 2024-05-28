<?php

namespace App\Http\Controllers\Invoice\OfflineTransaction;

use App\Actions\Invoice\OfflineTransaction\VerifyOfflineTransactionAction;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Exceptions\SystemException\OfflinePaymentApplyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\OfflineTransaction\VerifyOfflineTransactionRequest;
use App\Http\Resources\Invoice\OfflineTransaction\ShowOfflineTransactionResource;
use App\Models\OfflineTransaction;

class VerifyOfflineTransactionController extends Controller
{
    public function __construct(private readonly VerifyOfflineTransactionAction $offlineTransactionAction)
    {
        parent::__construct();
    }

    /**
     * @param OfflineTransaction $offlineTransaction
     * @return ShowOfflineTransactionResource
     * @throws OfflinePaymentApplyException
     * @throws InvoiceLockedAndAlreadyImportedToRahkaranException
     */
    public function __invoke(OfflineTransaction $offlineTransaction, VerifyOfflineTransactionRequest $request)
    {
        ($this->offlineTransactionAction)($offlineTransaction);

        return ShowOfflineTransactionResource::make($offlineTransaction);
    }
}

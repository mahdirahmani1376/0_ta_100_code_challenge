<?php

namespace App\Http\Controllers\Admin\OfflineTransaction;

use App\Actions\Admin\OfflineTransaction\VerifyOfflineTransactionAction;
use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Exceptions\SystemException\OfflinePaymentApplyException;
use App\Http\Requests\Admin\OfflineTransaction\VerifyOfflineTransactionRequest;
use App\Http\Resources\Admin\OfflineTransaction\OfflineTransactionResource;
use App\Models\OfflineTransaction;

class VerifyOfflineTransactionController
{
    public function __construct(private readonly VerifyOfflineTransactionAction $offlineTransactionAction)
    {
    }

    /**
     * @param OfflineTransaction $offlineTransaction
     * @return OfflineTransactionResource
     * @throws OfflinePaymentApplyException
     * @throws InvoiceLockedAndAlreadyImportedToRahkaranException
     */
    public function __invoke(OfflineTransaction $offlineTransaction, VerifyOfflineTransactionRequest $request)
    {
        ($this->offlineTransactionAction)($offlineTransaction);

        return OfflineTransactionResource::make($offlineTransaction);
    }
}

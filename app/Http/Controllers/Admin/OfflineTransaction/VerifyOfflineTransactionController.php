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
    private VerifyOfflineTransactionAction $offlineTransactionAction;

    public function __construct(VerifyOfflineTransactionAction $offlineTransactionAction)
    {
        $this->offlineTransactionAction = $offlineTransactionAction;
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

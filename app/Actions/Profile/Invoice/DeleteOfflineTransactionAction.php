<?php

namespace App\Actions\Profile\Invoice;

use App\Exceptions\Http\BadRequestException;
use App\Models\Invoice;
use App\Models\OfflineTransaction;
use App\Models\Transaction;
use App\Services\Profile\Invoice\DeleteOfflineTransactionService;
use App\Services\Profile\Invoice\UpdateTransactionService;
use App\Services\Transaction\FindTransactionByTrackingCodeService;

class DeleteOfflineTransactionAction
{
    private UpdateTransactionService $updateTransactionService;
    private DeleteOfflineTransactionService $deleteOfflineTransactionService;
    private FindTransactionByTrackingCodeService $findTransactionByTrackingCodeService;

    public function __construct(
        UpdateTransactionService        $updateTransactionService,
        DeleteOfflineTransactionService $deleteOfflineTransactionService,
        FindTransactionByTrackingCodeService $findTransactionByTrackingCodeService,
    )
    {
        $this->updateTransactionService = $updateTransactionService;
        $this->deleteOfflineTransactionService = $deleteOfflineTransactionService;
        $this->findTransactionByTrackingCodeService = $findTransactionByTrackingCodeService;
    }

    public function __invoke(Invoice $invoice, OfflineTransaction $offlineTransaction)
    {
        check_rahkaran($invoice);

        if ($offlineTransaction->status === OfflineTransaction::STATUS_PENDING) {
            throw new BadRequestException(trans('finance.error.OnlyPendingOfflinePaymentAllowed'));
        }

        if (!in_array($invoice->status, [Invoice::STATUS_UNPAID, Invoice::STATUS_PAYMENT_PENDING])) {
            throw new BadRequestException(trans('finance.error.OnlyUnpaidInvoiceAllowed'));
        }

        $transaction = ($this->findTransactionByTrackingCodeService)($offlineTransaction->tracking_code);
        ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_CANCELED]);

        return ($this->deleteOfflineTransactionService)($offlineTransaction);
    }
}

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
    public function __construct(
        private readonly UpdateTransactionService             $updateTransactionService,
        private readonly DeleteOfflineTransactionService      $deleteOfflineTransactionService,
        private readonly FindTransactionByTrackingCodeService $findTransactionByTrackingCodeService,
    )
    {
    }

    public function __invoke(OfflineTransaction $offlineTransaction)
    {
        check_rahkaran($offlineTransaction->invoice);

        if ($offlineTransaction->status !== OfflineTransaction::STATUS_PENDING) {
            throw new BadRequestException(__('finance.error.OnlyPendingOfflinePaymentAllowed'));
        }

        if (!in_array($offlineTransaction->invoice->status, [Invoice::STATUS_UNPAID, Invoice::STATUS_PAYMENT_PENDING])) {
            throw new BadRequestException(__('finance.error.OnlyUnpaidInvoiceAllowed'));
        }

        $transaction = ($this->findTransactionByTrackingCodeService)($offlineTransaction->tracking_code);
        ($this->updateTransactionService)($transaction, ['status' => Transaction::STATUS_CANCELED]);

        return ($this->deleteOfflineTransactionService)($offlineTransaction);
    }
}

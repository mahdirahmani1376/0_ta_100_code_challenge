<?php

namespace App\Actions\Invoice\Transaction;

use App\Actions\Invoice\ProcessInvoiceAction;
use App\Exceptions\Http\BadRequestException;
use App\Models\AdminLog;
use App\Models\Transaction;
use App\Services\Invoice\CalcInvoicePriceFieldsService;
use App\Services\Invoice\Transaction\VerifyTransactionService;

class VerifyTransactionAction
{
    public function __construct(
        private readonly VerifyTransactionService      $verifyTransactionService,
        private readonly ProcessInvoiceAction          $processInvoiceAction,
        private readonly CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService,
    )
    {
    }

    public function __invoke(Transaction $transaction)
    {
        check_rahkaran($transaction->invoice);

        $oldState = $transaction->toArray();

        if (!in_array($transaction->status, [
            Transaction::STATUS_PENDING,
            Transaction::STATUS_PENDING_BANK_VERIFY,
        ])) {
            throw new BadRequestException(trans('finance.ipg.OnlyUnknownAndReadyToPayStatusAreAllowed'));
        }

        ($this->verifyTransactionService)($transaction);
        $invoice = ($this->calcInvoicePriceFieldsService)($transaction->invoice);
        ($this->processInvoiceAction)($invoice);

        admin_log(AdminLog::VERIFY_TRANSACTION, $transaction, $transaction->getChanges(), $oldState);

        return $transaction;
    }
}

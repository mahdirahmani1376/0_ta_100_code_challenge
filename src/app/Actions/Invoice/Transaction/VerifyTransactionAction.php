<?php

namespace App\Actions\Invoice\Transaction;

use App\Actions\Invoice\ProcessInvoiceAction;
use App\Exceptions\Http\BadRequestException;
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

        if (!in_array($transaction->status, [
            Transaction::STATUS_PENDING,
            Transaction::STATUS_PENDING_BANK_VERIFY,
            Transaction::STATUS_FAIL,
            Transaction::STATUS_FRAUD,
        ])) {
            throw new BadRequestException(trans('finance.ipg.OnlyUnknownAndReadyToPayStatusAreAllowed'));
        }

        ($this->verifyTransactionService)($transaction);
        $invoice = ($this->calcInvoicePriceFieldsService)($transaction->invoice);
        ($this->processInvoiceAction)($invoice);


        return $transaction;
    }
}

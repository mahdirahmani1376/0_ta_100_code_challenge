<?php

namespace App\Actions\Admin\Invoice\Transaction;

use App\Actions\Admin\Invoice\ProcessInvoiceAction;
use App\Exceptions\Http\BadRequestException;
use App\Models\Transaction;
use App\Services\Admin\Transaction\VerifyTransactionService;

class VerifyTransactionAction
{
    private VerifyTransactionService $verifyTransactionService;
    private ProcessInvoiceAction $processInvoiceAction;

    public function __construct(
        VerifyTransactionService $verifyTransactionService,
        ProcessInvoiceAction     $processInvoiceAction
    )
    {
        $this->verifyTransactionService = $verifyTransactionService;
        $this->processInvoiceAction = $processInvoiceAction;
    }

    public function __invoke(Transaction $transaction)
    {
        check_rahkaran($transaction->invoice);

        if (!in_array($transaction->status, [
            Transaction::STATUS_PENDING,
            Transaction::STATUS_PENDING_BANK_VERIFY,
        ])) {
            throw new BadRequestException(trans('finance.ipg.OnlyUnknownAndReadyToPayStatusAreAllowed'));
        }

        ($this->verifyTransactionService)($transaction);
        ($this->processInvoiceAction)($transaction->invoice); // TODO check

        return $transaction;
    }
}

<?php

namespace App\Actions\Admin\Invoice\Transaction;

use App\Actions\Admin\Invoice\ProcessInvoiceAction;
use App\Exceptions\Http\BadRequestException;
use App\Models\AdminLog;
use App\Models\Transaction;
use App\Services\Admin\Transaction\VerifyTransactionService;

class VerifyTransactionAction
{
    public function __construct(
        private readonly VerifyTransactionService $verifyTransactionService,
        private readonly ProcessInvoiceAction     $processInvoiceAction
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
        ($this->processInvoiceAction)($transaction->invoice); // TODO check

        admin_log(AdminLog::VERIFY_TRANSACTION, $transaction, $transaction->getChanges(), $oldState);

        return $transaction;
    }
}

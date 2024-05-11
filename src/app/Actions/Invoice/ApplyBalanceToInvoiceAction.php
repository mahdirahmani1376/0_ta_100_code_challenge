<?php

namespace App\Actions\Invoice;

use App\Actions\Invoice\Transaction\StoreTransactionAction;
use App\Actions\Wallet\CreditTransaction\DeductBalanceAction;
use App\Actions\Wallet\ShowWalletAction;
use App\Exceptions\Http\BadRequestException;
use App\Exceptions\SystemException\AmountIsMoreThanInvoiceBalanceException;
use App\Exceptions\SystemException\ApplyCreditToCreditInvoiceException;
use App\Exceptions\SystemException\InvoiceStatusMustBeUnpaidException;
use App\Exceptions\SystemException\NotEnoughCreditException;
use App\Models\AdminLog;
use App\Models\Invoice;
use App\Models\Transaction;

class ApplyBalanceToInvoiceAction
{
    public function __construct(
        private readonly ShowWalletAction       $showWalletAction,
        private readonly DeductBalanceAction    $deductBalanceAction,
        private readonly StoreTransactionAction $storeTransactionAction,
        private readonly ProcessInvoiceAction   $processInvoiceAction,
    )
    {
    }

    public function __invoke(Invoice $invoice, array $data): Invoice
    {
        check_rahkaran($invoice);

        if (!empty($data['profile_id']) && $data['profile_id'] != $invoice->profile_id) {
            throw new BadRequestException(__('finance.invoice.AccessDeniedToInvoice'));
        }

        if (empty($data['amount'])) {
            $data['amount'] = $invoice->balance;
        }

        $oldState = $invoice->toArray();

        if (!in_array($invoice->status, [
            Invoice::STATUS_UNPAID,
            Invoice::STATUS_PAYMENT_PENDING,
            Invoice::STATUS_COLLECTIONS,
        ])) {
            throw InvoiceStatusMustBeUnpaidException::make();
        }

        if ($data['amount'] > $invoice->balance) {
            throw AmountIsMoreThanInvoiceBalanceException::make();
        }

        if ($invoice->is_credit) {
            throw ApplyCreditToCreditInvoiceException::make();
        }

        $wallet = ($this->showWalletAction)(profileId: $invoice->profile_id, recalculateBalance: true);

        if ($wallet->balance <= 0) {
            throw NotEnoughCreditException::make();
        }

        if ($data['amount'] > $wallet->balance) {
            $data['amount'] = $wallet->balance;
        }

        ($this->deductBalanceAction)($invoice->profile_id, [
            'amount'      => $data['amount'],
            'description' => __('finance.credit.ApplyCreditToInvoice', ['invoice_id' => $invoice->getKey()]),
            'invoice_id'  => $invoice->id
        ]);

        ($this->storeTransactionAction)([
            'invoice_id'     => $invoice->id,
            'amount'         => $data['amount'],
            'payment_method' => Transaction::PAYMENT_METHOD_WALLET_BALANCE,
        ]);

        admin_log(AdminLog::ADD_CREDIT_TO_INVOICE, $invoice, $invoice->getChanges(), $oldState, $data);

        $invoice->refresh();

        if ($invoice->balance <= 0) {
            ($this->processInvoiceAction)($invoice);
        }

        return $invoice;
    }
}

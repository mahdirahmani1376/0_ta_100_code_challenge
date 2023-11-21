<?php

namespace App\Actions\Admin\Invoice;

use App\Actions\Admin\Invoice\Transaction\StoreTransactionAction;
use App\Actions\Admin\Wallet\DeductBalanceAction;
use App\Actions\Admin\Wallet\ShowWalletAction;
use App\Actions\Invoice\ProcessInvoiceAction;
use App\Exceptions\Http\BadRequestException;
use App\Exceptions\SystemException\AmountIsMoreThanInvoiceBalanceException;
use App\Exceptions\SystemException\ApplyCreditToCreditInvoiceException;
use App\Models\AdminLog;
use App\Models\Invoice;
use App\Models\Transaction;

class ApplyBalanceToInvoiceAction
{
    public function __construct(
        private readonly ShowWalletAction       $showWalletAction,
        private readonly DeductBalanceAction    $deductBalanceAction,
        private readonly StoreTransactionAction $storeTransactionAction,
        private readonly ProcessInvoiceAction $processInvoiceAction,
    )
    {
    }

    public function __invoke(Invoice $invoice, array $data)
    {
        check_rahkaran($invoice);

        if (is_null($data['amount'])) {
            $data['amount'] = $invoice->balance;
        }

        $oldState = $invoice->toArray();

        if (!in_array($invoice->status, [
            Invoice::STATUS_UNPAID,
            Invoice::STATUS_PAYMENT_PENDING,
            Invoice::STATUS_COLLECTIONS,
        ])) {
            throw ApplyCreditToCreditInvoiceException::make();
        }

        if ($data['amount'] > $invoice->balance) {
            throw AmountIsMoreThanInvoiceBalanceException::make();
        }

        if ($invoice->is_credit) {
            throw ApplyCreditToCreditInvoiceException::make();
        }

        $wallet = ($this->showWalletAction)($invoice->profile_id);

        if ($data['amount'] > $wallet->balance) {
            throw new BadRequestException(__('finance.credit.NotEnoughBalance'));
        }

        ($this->deductBalanceAction)($invoice->profile_id, [
            'amount' => $data['amount'],
            'description' => __('finance.credit.ApplyCreditToInvoice', ['invoice_id' => $invoice->getKey()]),
        ]);

        ($this->storeTransactionAction)($invoice, [
            'amount' => $data['amount'],
            'payment_method' => Transaction::PAYMENT_METHOD_WALLET_BALANCE,
            'status' => Transaction::STATUS_SUCCESS,
        ]);

        admin_log(AdminLog::ADD_CREDIT_TO_INVOICE, $invoice, $invoice->getChanges(), $oldState, $data);

        $invoice->refresh();
        if ($invoice->balance == 0) {
            ($this->processInvoiceAction)($invoice);
        }

        return $invoice;
    }
}

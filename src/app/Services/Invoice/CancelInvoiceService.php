<?php

namespace App\Services\Invoice;

use App\Actions\Invoice\ChargeWalletInvoiceAction;
use App\Actions\Invoice\ProcessInvoiceAction;
use App\Actions\Wallet\CreditTransaction\StoreCreditTransactionAction;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;
use App\Services\Invoice\Transaction\AttachTransactionToNewInvoiceService;
use App\Services\Transaction\RefundTransactionService;

class CancelInvoiceService
{
    public function __construct(
        private readonly InvoiceRepositoryInterface           $invoiceRepository,
        private readonly RefundTransactionService             $refundTransactionService,
        private readonly StoreCreditTransactionAction         $storeCreditTransactionAction,
        private readonly ChargeWalletInvoiceAction            $chargeWalletInvoiceAction,
        private readonly AttachTransactionToNewInvoiceService $attachTransactionToNewInvoiceService,
        private readonly ProcessInvoiceAction                 $processInvoiceAction,
        private readonly TransactionRepositoryInterface       $transactionRepository,
        private readonly CalcInvoicePriceFieldsService        $calcInvoicePriceFieldsService,
    )
    {
    }

    public function __invoke(Invoice $invoice): Invoice
    {
        $invoice = $this->invoiceRepository->update($invoice, ['status' => Invoice::STATUS_CANCELED], ['status']);

        // get all wallet transactions and create credit transaction and transaction => status refund
        $walletRefundAmount = ($this->refundTransactionService)($invoice, false);
        if ($walletRefundAmount > 0) {
            ($this->storeCreditTransactionAction)($invoice->profile_id, [
                'amount'      => $walletRefundAmount,
                'description' => __('finance.credit.RefundCancelledInvoice', ['invoice_id' => $invoice->getKey()]),
                'invoice_id'  => $invoice->id
            ]);
        }

        // get all bank transactions and create credit invoice and assign transactions to new invoice and set as paid
        $onlineTransactions = $this->transactionRepository->paidTransactions($invoice, true);

        $amount = $onlineTransactions->sum('amount');
        if ($amount > 0) {
            $chargeWalletInvoice = ($this->chargeWalletInvoiceAction)([
                'profile_id' => $invoice->profile_id,
                'admin_id'   => request('admin_id'),
                'amount'     => $amount,
            ]);

            $onlineTransactions->each(function (Transaction $transaction) use ($chargeWalletInvoice) {
                ($this->attachTransactionToNewInvoiceService)($transaction, $chargeWalletInvoice);
            });
            $chargeWalletInvoice = ($this->calcInvoicePriceFieldsService)($chargeWalletInvoice);
            ($this->processInvoiceAction)($chargeWalletInvoice);
        }

        return $invoice;
    }
}

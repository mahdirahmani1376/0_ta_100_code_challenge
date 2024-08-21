<?php

namespace App\Actions\Invoice\OfflineTransaction;

use App\Actions\Invoice\ApplyBalanceToInvoiceAction;
use App\Actions\Invoice\ChargeWalletInvoiceAction;
use App\Actions\Invoice\Item\UpdateItemAction;
use App\Actions\Invoice\ProcessInvoiceAction;
use App\Actions\Invoice\Transaction\VerifyTransactionAction;
use App\Exceptions\SystemException\NotAuthorizedException;
use App\Exceptions\SystemException\OfflinePaymentApplyException;
use App\Models\Invoice;
use App\Models\OfflineTransaction;
use App\Services\Invoice\CalcInvoicePriceFieldsService;
use App\Services\Invoice\Item\FindAddCreditItemService;
use App\Services\Invoice\OfflineTransaction\AttachOfflineTransactionToNewInvoiceService;
use App\Services\Invoice\OfflineTransaction\VerifyOfflineTransactionService;
use App\Services\Invoice\Transaction\AttachTransactionToNewInvoiceService;

class VerifyOfflineTransactionAction
{
    public function __construct(
        private readonly FindAddCreditItemService                    $findAddCreditItemService,
        private readonly UpdateItemAction                            $updateItemAction,
        private readonly ProcessInvoiceAction                        $processInvoiceAction,
        private readonly ChargeWalletInvoiceAction                   $chargeWalletInvoiceAction,
        private readonly AttachOfflineTransactionToNewInvoiceService $attachOfflineTransactionToNewInvoiceService,
        private readonly AttachTransactionToNewInvoiceService        $attachTransactionToNewInvoiceService,
        private readonly ApplyBalanceToInvoiceAction                 $applyBalanceToInvoiceAction,
        private readonly VerifyOfflineTransactionService             $verifyOfflineTransactionService,
        private readonly VerifyTransactionAction                     $verifyTransactionAction,
        private readonly CalcInvoicePriceFieldsService               $calcInvoicePriceFieldsService
    )
    {
    }

    public function __invoke(OfflineTransaction $offlineTransaction)
    {
        $invoice = $offlineTransaction->invoice;

        if (!in_array($invoice->status, [Invoice::STATUS_UNPAID, Invoice::STATUS_COLLECTIONS, Invoice::STATUS_PAYMENT_PENDING])) {
            check_rahkaran($invoice);
        }

        if ($offlineTransaction->status === OfflineTransaction::STATUS_REJECTED) {
            throw NotAuthorizedException::make();
        }
        if ($offlineTransaction->status === OfflineTransaction::STATUS_CONFIRMED) {
            throw OfflinePaymentApplyException::make($offlineTransaction->getKey());
        }

        // If Invoice is type of credit and client paid a different amount than what he was supposed to
        // then update that Invoice's Item with type of AddCredit or AddCloudCredit with the amount of
        // this $offlineTransaction->amount PLUS the sum of successful transactions on that invoice (total - balance)
        if ($invoice->is_credit && $invoice->balance != $offlineTransaction->amount) {
            $addCreditOrCloudCreditItems = ($this->findAddCreditItemService)($invoice);
            if (!is_null($addCreditOrCloudCreditItems)) {
                ($this->updateItemAction)($invoice, $addCreditOrCloudCreditItems, [
                    'amount' => $offlineTransaction->amount + ($invoice->total - $invoice->balance),
                ]);
                $invoice->refresh();
            }
        }

        if ($invoice->is_credit) {
            ($this->verifyOfflineTransactionService)($offlineTransaction);
            ($this->verifyTransactionAction)($offlineTransaction->transaction);
        } else {
            // create a charge wallet invoice
            // attach this offlineTransaction and its transaction to the new invoice
            // pay the new charge-wallet-invoice -> because is_credit -> charge the wallet
            // use the wallet balance to pay the first invoice
            $chargeWalletInvoice = ($this->chargeWalletInvoiceAction)([
                'profile_id' => $invoice->profile_id,
                'admin_id'   => request('admin_id'),
                'amount'     => $offlineTransaction->amount,
            ]);
            ($this->attachOfflineTransactionToNewInvoiceService)($offlineTransaction, $chargeWalletInvoice);
            ($this->attachTransactionToNewInvoiceService)($offlineTransaction->transaction, $chargeWalletInvoice);
            ($this->calcInvoicePriceFieldsService)($chargeWalletInvoice);
            ($this->processInvoiceAction)($chargeWalletInvoice);

            if ($offlineTransaction->amount < $invoice->balance) {
                $amount = $offlineTransaction->amount;
            } else {
                $amount = $invoice->balance;
            }
            ($this->applyBalanceToInvoiceAction)($invoice, ['amount' => $amount]);
        }


        return $offlineTransaction;
    }
}

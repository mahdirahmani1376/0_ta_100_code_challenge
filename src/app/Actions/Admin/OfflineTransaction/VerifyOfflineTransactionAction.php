<?php

namespace App\Actions\Admin\OfflineTransaction;

use App\Actions\Admin\Invoice\ApplyBalanceToInvoiceAction;
use App\Actions\Admin\Invoice\ChargeWalletInvoiceAction;
use App\Actions\Admin\Invoice\Item\UpdateItemAction;
use App\Actions\Invoice\ProcessInvoiceAction;
use App\Exceptions\SystemException\NotAuthorizedException;
use App\Exceptions\SystemException\OfflinePaymentApplyException;
use App\Models\AdminLog;
use App\Models\OfflineTransaction;
use App\Services\Admin\Invoice\Item\FindAddCreditItemService;
use App\Services\Admin\OfflineTransaction\AttachOfflineTransactionToNewInvoiceService;
use App\Services\Admin\OfflineTransaction\VerifyOfflineTransactionService;
use App\Services\Admin\Transaction\AttachTransactionToNewInvoiceService;

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
    )
    {
    }

    public function __invoke(OfflineTransaction $offlineTransaction)
    {
        check_rahkaran($offlineTransaction->invoice);

        if ($offlineTransaction->status === OfflineTransaction::STATUS_REJECTED) {
            throw NotAuthorizedException::make();
        }
        if ($offlineTransaction->status === OfflineTransaction::STATUS_CONFIRMED) {
            throw OfflinePaymentApplyException::make($offlineTransaction->getKey());
        }

        $invoice = $offlineTransaction->invoice;
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

        if ($offlineTransaction->amount <= $invoice->balance || $invoice->is_credit) {
            ($this->verifyOfflineTransactionService)($offlineTransaction);
            ($this->processInvoiceAction)($invoice);
        } else {
            // create a charge wallet invoice
            // attach this offlineTransaction and its transaction to the new invoice
            // pay the new charge-wallet-invoice -> because is_credit -> charge the wallet
            // use the wallet balance to pay the first invoice
            $chargeWalletInvoice = ($this->chargeWalletInvoiceAction)([
                'client_id' => $invoice->client_id,
                'admin_id' => request('admin_id'),
                'amount' => $offlineTransaction->amount,
            ]);
            ($this->attachOfflineTransactionToNewInvoiceService)($offlineTransaction, $chargeWalletInvoice);
            ($this->attachTransactionToNewInvoiceService)($offlineTransaction->transaction, $chargeWalletInvoice);
            ($this->processInvoiceAction)($chargeWalletInvoice);

            ($this->applyBalanceToInvoiceAction)($invoice, ['amount' => $offlineTransaction->amount]);
            ($this->processInvoiceAction)($invoice);
        }

        admin_log(AdminLog::VERIFY_OFFLINE_TRANSACTION,$offlineTransaction);

        return $offlineTransaction;
    }
}

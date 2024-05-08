<?php

namespace App\Actions\Invoice;

use App\Actions\Wallet\CreditTransaction\StoreCreditTransactionAction;
use App\Models\Invoice;
use App\Services\Invoice\CancelInvoiceService;
use App\Services\Transaction\RefundTransactionService;

class CancelInvoiceAction
{
    public function __construct(
        private readonly CancelInvoiceService         $cancelInvoiceService,
        private readonly RefundTransactionService     $refundTransactionService,
        private readonly StoreCreditTransactionAction $storeCreditTransactionAction
    )
    {
    }

    public function __invoke(Invoice $invoice)
    {
        check_rahkaran($invoice);

        if (!in_array($invoice->status, [Invoice::STATUS_UNPAID, Invoice::STATUS_DRAFT])) {
            return $invoice;
        }

        ($this->cancelInvoiceService)($invoice);
        // Get sum of successful transactions of this invoice
        // i.e. how much the client actually paid for this invoice, so we can refund it
        // and if it was more than zero, return it to client's Wallet
        $refundAmount = ($this->refundTransactionService)($invoice);
        if ($refundAmount > 0) {
            ($this->storeCreditTransactionAction)($invoice->profile_id, [
                'amount'      => $refundAmount,
                'description' => __('finance.credit.RefundCancelledInvoice', ['invoice_id' => $invoice->getKey()]),
                'invoice_id'  => $invoice->getKey()
            ]);
        }

        return $invoice;
    }
}

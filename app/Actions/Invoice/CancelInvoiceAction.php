<?php
// TODO im not sure about this class's namespace and position in folder hierarchy , rethink about this later
namespace App\Actions\Invoice;

use App\Actions\Wallet\StoreCreditTransactionAction;
use App\Models\Invoice;
use App\Services\Invoice\CancelInvoiceService;
use App\Services\Transaction\RefundTransactionService;

class CancelInvoiceAction
{
    private CancelInvoiceService $cancelInvoiceService;
    private RefundTransactionService $refundTransactionService;
    private StoreCreditTransactionAction $storeCreditTransactionAction;

    public function __construct(
        CancelInvoiceService         $cancelInvoiceService,
        RefundTransactionService     $refundTransactionService,
        StoreCreditTransactionAction $storeCreditTransactionAction
    )
    {
        $this->cancelInvoiceService = $cancelInvoiceService;
        $this->refundTransactionService = $refundTransactionService;
        $this->storeCreditTransactionAction = $storeCreditTransactionAction;
    }

    public function __invoke(Invoice $invoice)
    {
        check_rahkaran($invoice);

        if ($invoice->status != Invoice::STATUS_UNPAID) {
            return $invoice;
        }

        ($this->cancelInvoiceService)($invoice);
        // Get sum of successful transactions of this invoice
        // i.e. how much the client actually paid for this invoice, so we can refund it
        // and if it was more than zero, return it to client's Wallet
        $refundAmount = ($this->refundTransactionService)($invoice);
        if ($refundAmount > 0) {
            ($this->storeCreditTransactionAction)($invoice->client_id, [
                'amount' => $refundAmount,
                'description' => __('finance.credit.RefundCancelledInvoice', ['invoice_id' => $invoice->getKey()]),
            ]);
        }

        return $invoice;
    }
}

<?php
// TODO low priority - reconsider this Action's namespace, i think it should be moved under public namespace but then what about its dependencies ?!
namespace App\Actions\Admin\Invoice;

use App\Actions\Admin\Wallet\ShowWalletAction;
use App\Actions\Admin\Wallet\StoreCreditTransactionAction;
use App\Jobs\AssignInvoiceNumberJob;
use App\Models\Invoice;
use App\Services\Admin\Invoice\ChangeInvoiceStatusService;
use App\Services\Admin\Transaction\StoreRefundTransactionService;
use App\Services\Admin\Wallet\StoreCreditTransactionForOfflineTransactionService;
use App\Services\Invoice\CalcInvoicePaidAtService;
use App\Services\Invoice\CalcInvoicePriceFieldsService;
use App\Services\Wallet\CalcWalletBalanceService;

class ProcessInvoiceAction
{
    public function __construct(
        private readonly ShowWalletAction                                   $showWalletAction,
        private readonly StoreRefundTransactionService                      $storeRefundTransactionService,
        private readonly ChangeInvoiceStatusService                         $changeInvoiceStatusService,
        private readonly CalcInvoicePaidAtService                           $calcInvoicePaidAtService,
        private readonly StoreCreditTransactionAction                       $storeCreditTransactionAction,
        private readonly StoreCreditTransactionForOfflineTransactionService $storeCreditTransactionForOfflineTransactionService,
        private readonly CalcWalletBalanceService                           $calcWalletBalanceService,
        private readonly CalcInvoicePriceFieldsService                      $calcInvoicePriceFieldsService,
    )
    {
    }
    // TODO check ProcessInvoiceAction logic - e.g. balance == 0 ?
    // TODO check usage of this action
    // TODO make sure to not process an already processed Invoice twice
    public function __invoke(Invoice $invoice): Invoice
    {
        // If REFUNDED Invoice then charge client's wallet and store a transaction for this Invoice
        if ($invoice->status === Invoice::STATUS_REFUNDED) {
            ($this->storeCreditTransactionAction)($invoice->client_id, [
                'amount' => $invoice->total,
                'description' => __('finance.credit.RefundRefundedInvoiceCredit', ['invoice_id' => $invoice->getKey()]),
            ]);
            ($this->storeRefundTransactionService)($invoice);
            ($this->calcInvoicePriceFieldsService)($invoice);
        }

        // Change status to paid unless it is a REFUND invoice
        if (!in_array($invoice->status, [
            Invoice::STATUS_PAID,
            Invoice::STATUS_COLLECTIONS,
            Invoice::STATUS_REFUNDED,
        ])) {
            ($this->changeInvoiceStatusService)($invoice, Invoice::STATUS_PAID);
        }
        // Calc paid_at
        if (is_null($invoice->paid_at)) {
            ($this->calcInvoicePaidAtService)($invoice);
        }

        // Assign InvoiceNumber
        AssignInvoiceNumberJob::dispatch($invoice); // TODO when should we assign an InvoiceNumber,is it only when paid_at is set or what ?

        // If invoice is charge-wallet (is_credit=true),
        // create CreditTransaction records based on how many 'verified' OfflineTransactions this Invoice has and increase client's wallet balance
        if ($invoice->is_credit) {
            $wallet = ($this->showWalletAction)($invoice->client_id);
            ($this->storeCreditTransactionForOfflineTransactionService)($invoice, $wallet);
            ($this->calcWalletBalanceService)($wallet);
        }

        // Dispatch jobs TODO
        // Probably have to call an API from MainApp or something
        if ($invoice->balance == 0) {
            //DomainAfterPaymentJob::dispatch($invoice);
            //ServiceAfterPaymentJob::dispatch($invoice);
        }

        // TODO Invoice Affiliation ?

        return $invoice;
    }
}

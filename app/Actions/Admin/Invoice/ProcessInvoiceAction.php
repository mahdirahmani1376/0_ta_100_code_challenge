<?php

namespace App\Actions\Admin\Invoice;

use App\Actions\Admin\Wallet\ShowWalletAction;
use App\Actions\Admin\Wallet\StoreCreditTransactionAction;
use App\Models\Invoice;
use App\Services\Admin\Invoice\AssignInvoiceNumberService;
use App\Services\Admin\Invoice\ChangeInvoiceStatusService;
use App\Services\Admin\Transaction\StoreRefundCreditTransactionService;
use App\Services\Admin\Transaction\StoreRefundTransactionService;
use App\Services\Admin\Wallet\StoreCreditTransactionForOfflineTransactionService;
use App\Services\Invoice\CalcInvoicePaidAtService;
use App\Services\Wallet\CalcWalletBalanceService;

class ProcessInvoiceAction
{
    private ShowWalletAction $showWalletAction;
    private StoreRefundCreditTransactionService $storeRefundCreditTransactionService;
    private StoreRefundTransactionService $storeRefundTransactionService;
    private ChangeInvoiceStatusService $changeInvoiceStatusService;
    private CalcInvoicePaidAtService $calcInvoicePaidAtService;
    private StoreCreditTransactionAction $storeCreditTransactionAction;
    private AssignInvoiceNumberService $assignInvoiceNumberService;
    private StoreCreditTransactionForOfflineTransactionService $storeCreditTransactionForOfflineTransactionService;
    private CalcWalletBalanceService $calcWalletBalanceService;

    public function __construct(
        ShowWalletAction                                   $showWalletAction,
        StoreRefundCreditTransactionService                $storeRefundCreditTransactionService,
        StoreRefundTransactionService                      $storeRefundTransactionService,
        ChangeInvoiceStatusService                         $changeInvoiceStatusService,
        CalcInvoicePaidAtService                           $calcInvoicePaidAtService,
        StoreCreditTransactionAction                       $storeCreditTransactionAction,
        AssignInvoiceNumberService                         $assignInvoiceNumberService,
        StoreCreditTransactionForOfflineTransactionService $storeCreditTransactionForOfflineTransactionService,
        CalcWalletBalanceService                           $calcWalletBalanceService,
    )
    {
        $this->showWalletAction = $showWalletAction;
        $this->storeRefundCreditTransactionService = $storeRefundCreditTransactionService;
        $this->storeRefundTransactionService = $storeRefundTransactionService;
        $this->changeInvoiceStatusService = $changeInvoiceStatusService;
        $this->calcInvoicePaidAtService = $calcInvoicePaidAtService;
        $this->storeCreditTransactionAction = $storeCreditTransactionAction;
        $this->assignInvoiceNumberService = $assignInvoiceNumberService;
        $this->storeCreditTransactionForOfflineTransactionService = $storeCreditTransactionForOfflineTransactionService;
        $this->calcWalletBalanceService = $calcWalletBalanceService;
    }

    public function __invoke(Invoice $invoice): Invoice
    {
        // If REFUNDED Invoice then charge client's wallet and store a transaction for this Invoice
        if ($invoice->status === Invoice::STATUS_REFUNDED) {
            ($this->storeCreditTransactionAction)($invoice->client_id, [
                'amount' => $invoice->total,
                'description' => __('finance.credit.RefundRefundedInvoiceCredit', ['invoice_id' => $invoice->getKey()]),
            ])
            ($this->storeRefundTransactionService)($invoice);
        }

        // Change status to paid unless it is a REFUND invoice
        // TODO what about collection
        if ($invoice->status !== Invoice::STATUS_REFUNDED) {
            ($this->changeInvoiceStatusService)($invoice, Invoice::STATUS_PAID);
        }
        // Calc paid_at
        if (is_null($invoice->paid_at)) {
            ($this->calcInvoicePaidAtService)($invoice);
        }

        // Assign InvoiceNumber
        // TODO dispatch this service as a queued job
        // TODO can is_credit invoices have InvoiceNumber ?
        ($this->assignInvoiceNumberService)($invoice);

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

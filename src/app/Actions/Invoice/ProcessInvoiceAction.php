<?php

namespace App\Actions\Invoice;

use App\Actions\Wallet\CreditTransaction\StoreCreditTransactionAction;
use App\Jobs\AssignInvoiceNumberJob;
use App\Jobs\Invoice\InvoiceProcessedJob;
use App\Models\Invoice;
use App\Models\Item;
use App\Services\Invoice\CalcInvoicePaidAtService;
use App\Services\Invoice\CalcInvoiceProcessedAtService;
use App\Services\Invoice\ChangeInvoiceStatusService;
use App\Services\Invoice\FindInvoiceByIdService;
use App\Services\Invoice\ManualCheckService;
use App\Services\Invoice\Transaction\StoreRefundTransactionService;

class ProcessInvoiceAction
{
    public function __construct(
        private readonly StoreRefundTransactionService $storeRefundTransactionService,
        private readonly ChangeInvoiceStatusService    $changeInvoiceStatusService,
        private readonly CalcInvoicePaidAtService      $calcInvoicePaidAtService,
        private readonly StoreCreditTransactionAction  $storeCreditTransactionAction,
        private readonly FindInvoiceByIdService        $findInvoiceByIdService,
        private readonly CalcInvoiceProcessedAtService $calcInvoiceProcessedAtService,
        private readonly ManualCheckService            $manualCheckService,
        private readonly ShowInvoiceByCriteriaAction   $showInvoiceByCriteriaAction,
    )
    {
    }

    public function __invoke(Invoice $invoice): Invoice
    {
        // Get invoice and recalculate price fields
        $invoice = ($this->showInvoiceByCriteriaAction)([
            'id' => $invoice->getKey()
        ], true);

        // If REFUNDED Invoice then charge client's wallet and store a transaction for this Invoice
        if ($invoice->status === Invoice::STATUS_REFUNDED) {
            $this->processRefundedInvoice($invoice);
        }

        // Normal Invoices must have zero balance to be processed
        if ($invoice->status == Invoice::STATUS_UNPAID && $invoice->balance > 0) {
            return $invoice;
        }
        // Collection Invoices can have positive balance and still be processed,
        // but if an Invoice is not Collection then it MUST have zero balance otherwise cannot be processed until it's paid in full
        if ($invoice->status != Invoice::STATUS_COLLECTIONS && $invoice->balance > 0) {
            return $invoice;
        }

        // Change status to paid unless it is a REFUND invoice
        if (!in_array($invoice->status, [
            Invoice::STATUS_PAID,
            Invoice::STATUS_REFUNDED,
            Invoice::STATUS_COLLECTIONS
        ]) || ($invoice->status == Invoice::STATUS_COLLECTIONS && $invoice->balance == 0)) {
            ($this->changeInvoiceStatusService)($invoice, Invoice::STATUS_PAID);
        }


        // Calc paid_at
        if (is_null($invoice->paid_at)) {
            ($this->calcInvoicePaidAtService)($invoice);
        }

        // Assign InvoiceNumber
        try {
            AssignInvoiceNumberJob::dispatch($invoice); // TODO when should we assign an InvoiceNumber,is it only when paid_at is set or what ?
        } catch (\Throwable $throwable) {
            \Log::warning('Assign invoice number failed #' . $invoice->id, $throwable->getTrace());
        }

        // If invoice is charge-wallet or is mass payment (is_credit=true),
        // create CreditTransaction records based on how many 'verified' OfflineTransactions this Invoice has and increase client's wallet balance
        if ($invoice->is_credit && !$invoice->processed_at) {
            $this->storeCreditTraction($invoice, $invoice->total);
        }

        if ($invoice->is_credit && !$invoice->admin_id) {
            ($this->manualCheckService)($invoice, 1);
        }

        if ($invoice->is_mass_payment) {
            $mass_invoices = [];
            $amount = 0;
            $invoice->items->each(function (Item $item) use ($invoice, &$amount, &$mass_invoices) {
                $mass_invoice = ($this->findInvoiceByIdService)($item->invoiceable_id);
                if ($mass_invoice instanceof Invoice) {
                    $amount += round_amount($mass_invoice?->balance ?? 0);
                    $mass_invoices[] = $mass_invoice;
                }
            });

            if ($amount > 0) {
                $this->storeCreditTraction($invoice, $amount);
            }

            $applyBalanceToInvoiceAction = app()->make(ApplyBalanceToInvoiceAction::class);

            foreach ($mass_invoices as $mass_invoice) {
                ($applyBalanceToInvoiceAction)($mass_invoice, []);
            }
        }


        if (!$invoice->processed_at) {
            ($this->calcInvoiceProcessedAtService)($invoice);
            if (!$invoice->is_credit) {
                InvoiceProcessedJob::dispatch($invoice);
            }
        }

        return $invoice;
    }

    private function storeCreditTraction(Invoice $invoice, float $amount): void
    {
        ($this->storeCreditTransactionAction)($invoice->profile_id, [
            'amount'      => $amount,
            'description' => __('finance.credit.AddCreditInvoice', ['invoice_id' => $invoice->getKey()]),
            'invoice_id'  => $invoice->getKey(),
            'date'        => $invoice->paid_at
        ]);
    }

    private function processRefundedInvoice(Invoice $invoice): void
    {
        ($this->storeCreditTransactionAction)($invoice->profile_id, [
            'amount'      => $invoice->total,
            'description' => __('finance.credit.RefundRefundedInvoiceCredit', ['invoice_id' => $invoice->getKey()]),
            'invoice_id'  => $invoice->getKey()
        ]);
        ($this->storeRefundTransactionService)($invoice);
    }
}

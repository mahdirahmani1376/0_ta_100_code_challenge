<?php

namespace App\Actions\Invoice;

use App\Actions\Wallet\CreditTransaction\StoreCreditTransactionAction;
use App\Events\InvoiceProcessed;
use App\Jobs\AssignInvoiceNumberJob;
use App\Models\Invoice;
use App\Models\Item;
use App\Services\Invoice\CalcInvoicePaidAtService;
use App\Services\Invoice\CalcInvoicePriceFieldsService;
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
        private readonly CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService,
        private readonly FindInvoiceByIdService        $findInvoiceByIdService,
        private readonly CalcInvoiceProcessedAtService $calcInvoiceProcessedAtService,
        private readonly ManualCheckService            $manualCheckService,
    )
    {
    }

    // TODO check usage of this action
    public function __invoke(Invoice $invoice): Invoice
    {
        $invoice->refresh();

        // If REFUNDED Invoice then charge client's wallet and store a transaction for this Invoice

        if ($invoice->status === Invoice::STATUS_REFUNDED) {
            ($this->storeCreditTransactionAction)($invoice->profile_id, [
                'amount'      => $invoice->total,
                'description' => __('finance.credit.RefundRefundedInvoiceCredit', ['invoice_id' => $invoice->getKey()]),
                'invoice_id'  => $invoice->getKey()
            ]);
            ($this->storeRefundTransactionService)($invoice);
            ($this->calcInvoicePriceFieldsService)($invoice);
        }
        // If an Invoice is already processed then ignore it, this might happen when a Collection Invoice is paid at the end of the month,
        // so we only change its status to paid and nothing else, this is done in another service
        /*if (!is_null($invoice->processed_at)) {
            return $invoice;
        }*/
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

        // If invoice is charge-wallet or is mass payment (is_credit=true),
        // create CreditTransaction records based on how many 'verified' OfflineTransactions this Invoice has and increase client's wallet balance
        if ($invoice->is_credit) {
            $this->storeCreditTraction($invoice);
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
                    $amount += ceil($mass_invoice?->balance ?? 0 / 100) * 100;
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

        ($this->calcInvoiceProcessedAtService)($invoice);
        InvoiceProcessed::dispatch($invoice);

        return $invoice;
    }

    private function storeCreditTraction(Invoice $invoice, float $amount = 0): void
    {
        ($this->storeCreditTransactionAction)($invoice->profile_id, [
            'amount'      => $amount > 0 ? $amount : $invoice->balance,
            'description' => __('finance.credit.AddCreditInvoice', ['invoice_id' => $invoice->getKey()]),
            'invoice_id'  => $invoice->getKey()
        ]);
    }
}

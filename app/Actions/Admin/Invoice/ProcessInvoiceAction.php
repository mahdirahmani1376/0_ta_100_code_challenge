<?php
// TODO low priority - reconsider this Action's namespace, i think it should be moved under public namespace but then what about its dependencies ?!
namespace App\Actions\Admin\Invoice;

use App\Actions\Admin\Wallet\StoreCreditTransactionAction;
use App\Events\InvoiceProcessed;
use App\Exceptions\Http\BadRequestException;
use App\Jobs\AssignInvoiceNumberJob;
use App\Models\Invoice;
use App\Models\Item;
use App\Services\Admin\Invoice\ChangeInvoiceStatusService;
use App\Services\Admin\Transaction\StoreRefundTransactionService;
use App\Services\Invoice\CalcInvoicePaidAtService;
use App\Services\Invoice\CalcInvoicePriceFieldsService;
use App\Services\Invoice\FindInvoiceByIdService;

class ProcessInvoiceAction
{
    public function __construct(
        private readonly StoreRefundTransactionService                         $storeRefundTransactionService,
        private readonly ChangeInvoiceStatusService                            $changeInvoiceStatusService,
        private readonly CalcInvoicePaidAtService                              $calcInvoicePaidAtService,
        private readonly StoreCreditTransactionAction                          $storeCreditTransactionAction,
        private readonly CalcInvoicePriceFieldsService                         $calcInvoicePriceFieldsService,
        private readonly FindInvoiceByIdService                                $findInvoiceByIdService,
    )
    {
    }
    // TODO check ProcessInvoiceAction logic - e.g. balance == 0 ?
    // TODO check usage of this action
    // TODO make sure to not process an already processed Invoice twice
    // TODO if invoice status is changing from "collection" to "paid" make sure to not rerun all of the processInvoiceAction
    public function __invoke(Invoice $invoice, bool $usedToBeCollection = false): Invoice
    {
        $invoice->refresh();

        if ($invoice->status == Invoice::STATUS_UNPAID && $invoice->balance > 0) {
            throw new BadRequestException('Can not Process non-collection Invoice with positive balance, invoiceId: ' . $invoice->getKey());
        }
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
            ($this->storeCreditTransactionAction)($invoice->client_id, [
                'amount' => $invoice->total,
                'description' => __('finance.credit.AddCreditInvoice', ['invoice_id' => $invoice->getKey()]),
            ]);
        }

        if ($invoice->is_mass_payment) {
            $invoice->items()->each(function (Item $item) {
                $invoice = ($this->findInvoiceByIdService)($item->invoiceable_id);
                if (!is_null($invoice)) {
                    ($this)($invoice);
                }
            });
        }
        // TODO do we need to check for balance==0 ?
        if (!$usedToBeCollection) {
            InvoiceProcessed::dispatch($invoice);
            // TODO Invoice Affiliation ?
        }

        return $invoice;
    }
}

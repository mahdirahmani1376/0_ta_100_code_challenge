<?php

namespace App\Actions\Invoice\Transaction;

use App\Actions\Invoice\ProcessInvoiceAction;
use App\Exceptions\Http\BadRequestException;
use App\Models\Transaction;
use App\Models\Invoice;
use App\Services\Invoice\CalcInvoicePriceFieldsService;
use App\Services\Invoice\FindInvoiceByIdService;
use App\Services\Invoice\Transaction\StoreTransactionService;

class StoreTransactionAction
{
    public function __construct(
        private readonly FindInvoiceByIdService        $findInvoiceByIdService,
        private readonly CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService,
        private readonly StoreTransactionService       $storeTransactionService,
        private readonly ProcessInvoiceAction          $processInvoiceAction,
    )
    {
    }

    public function __invoke(array $data)
    {
        $invoice = ($this->findInvoiceByIdService)($data['invoice_id']);

        if (!in_array($invoice->status, [Invoice::STATUS_UNPAID, Invoice::STATUS_COLLECTIONS, Invoice::STATUS_PAYMENT_PENDING])) {
            check_rahkaran($invoice);
        }

        if ($invoice->balance < 0) {
            throw new BadRequestException(__('finance.invoice.NegativeBalance'));
        }
        if ($invoice->balance == 0 || $data['amount'] > $invoice->balance) {
            throw new BadRequestException(__('finance.invoice.AmountExceedsInvoiceBalance'));
        }

        $data['status'] = Transaction::STATUS_SUCCESS;
        $transaction = ($this->storeTransactionService)($invoice, $data);

        $invoice = ($this->calcInvoicePriceFieldsService)($invoice);

        ($this->processInvoiceAction)($invoice);

        return $transaction;
    }
}

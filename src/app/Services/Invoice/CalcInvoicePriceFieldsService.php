<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use App\Repositories\Invoice\Interface\ItemRepositoryInterface;
use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;

class CalcInvoicePriceFieldsService
{
    public function __construct(
        private readonly ItemRepositoryInterface        $itemRepository,
        private readonly InvoiceRepositoryInterface     $invoiceRepository,
        private readonly TransactionRepositoryInterface $transactionRepository,
    )
    {
    }

    public function __invoke(Invoice $invoice): Invoice
    {
        $invoice->refresh();

        $subTotal = $this->itemRepository->sumAmountByInvoice($invoice);
        $tax = ($subTotal * $invoice->tax_rate) / 100;
        $totalBeforeRounding = $subTotal + $tax;
        $total = ceil($totalBeforeRounding / 100) * 100;
        $sumOfPaidTransactions = $this->transactionRepository->sumOfPaidTransactions($invoice);
        $balance = $total - $sumOfPaidTransactions;

        return $this->invoiceRepository->update(
            $invoice,
            [
                'sub_total' => $subTotal,
                'tax'       => $tax,
                'total'     => $total,
                'balance'   => $balance,
            ],
            [
                'sub_total',
                'tax',
                'total',
                'balance',
            ],
        );
    }
}

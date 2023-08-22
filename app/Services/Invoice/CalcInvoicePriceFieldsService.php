<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use App\Repositories\Invoice\Interface\ItemRepositoryInterface;
use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;

class CalcInvoicePriceFieldsService
{
    private InvoiceRepositoryInterface $invoiceRepository;
    private ItemRepositoryInterface $itemRepository;
    private TransactionRepositoryInterface $transactionRepository;

    public function __construct(
        ItemRepositoryInterface        $itemRepository,
        InvoiceRepositoryInterface     $invoiceRepository,
        TransactionRepositoryInterface $transactionRepository,
    )
    {
        $this->invoiceRepository = $invoiceRepository;
        $this->itemRepository = $itemRepository;
        $this->transactionRepository = $transactionRepository;
    }

    public function __invoke(Invoice $invoice): Invoice
    {
        $invoice->refresh();

        $subTotal = $this->itemRepository->sumAmountByInvoice($invoice);

        $tax = ($subTotal * $invoice->tax_rate) / 100;

        $totalBeforeRounding = $subTotal + $tax;
        $total = round($totalBeforeRounding / 1000) * 1000;

        $sumOfPaidTransactions = $this->transactionRepository->sumOfPaidTransactions($invoice);
        $balance = $total - $sumOfPaidTransactions;

        return $this->invoiceRepository->update(
            $invoice,
            [
                'sub_total' => $subTotal,
                'tax' => $tax,
                'total' => $total,
                'balance' => $balance,
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

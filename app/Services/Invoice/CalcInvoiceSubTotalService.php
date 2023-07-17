<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use App\Repositories\Invoice\Interface\ItemRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class CalcInvoiceSubTotalService
{
    private InvoiceRepositoryInterface $invoiceRepository;
    private ItemRepositoryInterface $itemRepository;

    public function __construct(ItemRepositoryInterface    $itemRepository,
                                InvoiceRepositoryInterface $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
        $this->itemRepository = $itemRepository;
    }

    public function __invoke(Invoice $invoice): Invoice
    {
        $sumOfItems = $this->itemRepository->sumAmountByInvoice($invoice);

        return $this->invoiceRepository->update(
            $invoice,
            ['sub_total' => $sumOfItems,],
            ['sub_total'],
        );
    }
}

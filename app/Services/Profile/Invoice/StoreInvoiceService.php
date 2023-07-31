<?php

namespace App\Services\Profile\Invoice;

use App\Models\Invoice;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;

class StoreInvoiceService
{
    private InvoiceRepositoryInterface $invoiceRepository;

    public function __construct(InvoiceRepositoryInterface $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
    }

    public function __invoke(array $data)
    {
        $data['tax_rate'] = $data['tax_rate'] ?? Invoice::DEFAULT_TAX_RATE;
        $data['payment_method'] = Invoice::PAYMENT_METHOD_CREDIT;

        return $this->invoiceRepository->create($data, [
            'tax_rate',
            'payment_method',
            'status',
            'client_id',
            'is_credit',
        ]);
    }
}

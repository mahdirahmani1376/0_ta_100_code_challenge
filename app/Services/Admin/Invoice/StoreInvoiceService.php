<?php

namespace App\Services\Admin\Invoice;

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
        $data['tax_rate'] = Invoice::DEFAULT_TAX_RATE;
        $data['payment_method'] = Invoice::PAYMENT_METHOD_CREDIT;
        $data['created_at'] = $data['invoice_date'];
        if ($data['status'] == Invoice::STATUS_REFUNDED && empty($data['paid_at'])) {
            $data['paid_at'] = $data['created_at'];
        }

        return $this->invoiceRepository->create($data, [
            'tax_rate',
            'payment_method',
            'created_at',
            'status',
            'paid_at',
            'client_id',
        ]);
    }
}

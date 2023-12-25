<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;

class StoreInvoiceService
{
    public function __construct(private readonly InvoiceRepositoryInterface $invoiceRepository)
    {
    }

    public function __invoke(array $data)
    {
        $data['tax_rate'] = $data['tax_rate'] ?? Invoice::DEFAULT_TAX_RATE;
        $data['payment_method'] = Invoice::PAYMENT_METHOD_CREDIT;
        $data['created_at'] = $data['invoice_date'] ?? now();
        if ($data['status'] == Invoice::STATUS_REFUNDED && empty($data['paid_at'])) {
            $data['paid_at'] = $data['created_at'];
        }

        return $this->invoiceRepository->create($data);
    }
}

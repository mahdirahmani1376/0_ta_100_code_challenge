<?php

namespace App\Services\Invoice;

use App\Integrations\MainApp\MainAppConfig;
use App\Models\Invoice;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;

class StoreInvoiceService
{
    public function __construct(private readonly InvoiceRepositoryInterface $invoiceRepository)
    {
    }

    public function __invoke(array $data)
    {
        $data['due_date'] = data_get($data,'due_date') ?? now()->addDays(10);
        $data['tax_rate'] = $data['tax_rate'] ?? MainAppConfig::get(MainAppConfig::FINANCE_SERVICE_DEFAULT_TAX);
        $data['payment_method'] = Invoice::PAYMENT_METHOD_CREDIT;
        $data['created_at'] = $data['invoice_date'] ?? now();
        if ($data['status'] == Invoice::STATUS_REFUNDED && empty($data['paid_at'])) {
            $data['paid_at'] = $data['created_at'];
        }
        if (!empty($data['manual'])){
            $data['processed_at'] = now();
        }


        return $this->invoiceRepository->create($data);
    }
}

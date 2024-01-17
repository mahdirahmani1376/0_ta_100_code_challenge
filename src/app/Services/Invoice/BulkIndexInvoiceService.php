<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;

class BulkIndexInvoiceService
{
    public function __construct(private readonly InvoiceRepositoryInterface $invoiceRepository)
    {
    }

    public function __invoke(array $data): array
    {
        $result = [];
        foreach ($data['items'] as $item) {
            $invoice = $this->invoiceRepository->index([
                'export'           => 1,
                'per_page'         => 1,
                'status'           => $data['status'] ?? Invoice::STATUS_UNPAID,
                'invoiceable_id'   => $item['invoiceable_id'],
                'invoiceable_type' => $item['invoiceable_type'] ?? null,
            ]);

            if (empty($result[$item['invoiceable_id']])) {
                $result[$item['invoiceable_id']] = $invoice->isEmpty() ? null : $invoice->first();
            }
        }

        return $result;
    }
}
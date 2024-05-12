<?php

namespace App\Repositories\Invoice\Interface;

use App\Models\Invoice;
use App\Models\InvoiceNumber;
use App\Repositories\Base\Interface\EloquentRepositoryInterface;

interface InvoiceNumberRepositoryInterface extends EloquentRepositoryInterface
{
    public function findByInvoice(Invoice $invoice): ?InvoiceNumber;

    public function use(Invoice $invoice, string $type, string $fiscalYear, $invoiceNumber = null): int;

    public function getLatestInvoiceNumber(string $type, string $fiscalYear): ?int;

    public function countUnused(string $type, string $fiscalYear): int;
}

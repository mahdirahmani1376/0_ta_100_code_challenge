<?php

namespace App\Services\Admin\Invoice;

use App\Models\Invoice;
use App\Models\InvoiceNumber;
use App\Repositories\Invoice\Interface\InvoiceNumberRepositoryInterface;

class AssignInvoiceNumberService
{
    private InvoiceNumberRepositoryInterface $invoiceNumberRepository;

    public function __construct(InvoiceNumberRepositoryInterface $invoiceNumberRepository)
    {
        $this->invoiceNumberRepository = $invoiceNumberRepository;
    }

    public function __invoke(Invoice $invoice): ?InvoiceNumber
    {
        if (!in_array($invoice->status,[
            Invoice::STATUS_PAID,
            Invoice::STATUS_COLLECTIONS,
            Invoice::STATUS_REFUNDED,
        ])) {
            return null;
        }

        // MassPayment Invoices cant have InvoiceNumber
        if ($invoice->is_mass_payment) {
            return null;
        }

        // Cant assign InvoiceNumber to Invoices with zero total/sub_total
        if ($invoice->total === 0 && $invoice->sub_total === 0) {
            return null;
        }

        // Business requirement
        if ($invoice->getKey() < config('payment.invoice_number.current_invoice_id')) {
            return null;
        }

        $invoiceNumber = $this->invoiceNumberRepository->findByInvoice($invoice);
        // If Invoice already has an InvoiceNumber do nothing
        if (!is_null($invoiceNumber)) {
            return $invoiceNumber;
        }

        $type = $invoice->status == Invoice::STATUS_REFUNDED ? InvoiceNumber::TYPE_REFUND : InvoiceNumber::TYPE_PAID;

        $fiscalYear = config('payment.invoice_number.current_fiscal_year'); // TODO

        $invoiceNumber = $this->invoiceNumberRepository->getAvailableInvoiceNumber($type, $fiscalYear);

        // No available InvoiceNumber, generate 100 available InvoiceNumbers
        if (is_null($invoiceNumber)) {
            $latestInvoiceNumber = $this->invoiceNumberRepository->getLatestInvoiceNumber($type);
            $hundredAvailableInvoiceNumbers = [];
            $now = now();
            for ($i = 1; $i <= 100; $i++) {
                $hundredAvailableInvoiceNumbers[] = [
                    'created_at' => $now,
                    'updated_at' => $now,
                    'invoice_number' => $latestInvoiceNumber + $i,
                    'type' => $type,
                    'fiscal_year' => $fiscalYear,
                    'status' => InvoiceNumber::STATUS_UNUSED,
                ];
            }
            // Insert new available InvoiceNumbers
            $this->invoiceNumberRepository->insert($hundredAvailableInvoiceNumbers);

            // Try again to get an available InvoiceNumber
            $invoiceNumber = $this->invoiceNumberRepository->getAvailableInvoiceNumber($type, $fiscalYear);
        }

        $this->invoiceNumberRepository->use($invoice, $invoiceNumber);

        return $invoiceNumber;
    }
}

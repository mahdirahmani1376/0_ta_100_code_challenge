<?php
namespace App\Services\Invoice;

use App\Jobs\GenerateInvoiceNumberJob;
use App\Models\Invoice;
use App\Models\InvoiceNumber;
use App\Repositories\Invoice\Interface\InvoiceNumberRepositoryInterface;

class AssignInvoiceNumberService
{
    public function __construct(private readonly InvoiceNumberRepositoryInterface $invoiceNumberRepository)
    {
    }

    public function __invoke(Invoice $invoice, $invoiceNumber = null, $fiscalYear = null): ?InvoiceNumber
    {
        if (!in_array($invoice->status, [
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

        // Credit Invoices cant have InvoiceNumber
        if ($invoice->is_credit) {
            return null;
        }

        // Cant assign InvoiceNumber to Invoices with zero total/sub_total
        if ($invoice->total == 0 && $invoice->sub_total == 0) {
            return null;
        }

        // Business requirement
        if ($invoice->getKey() < config('payment.invoice_number.current_invoice_id')) {
            return null;
        }

        $invoiceNumberModel = $this->invoiceNumberRepository->findByInvoice($invoice);
        // If Invoice already has an InvoiceNumber do nothing
        if (!is_null($invoiceNumberModel)) {
            return $invoiceNumberModel;
        }

        $type = $invoice->status == Invoice::STATUS_REFUNDED ? InvoiceNumber::TYPE_REFUND : InvoiceNumber::TYPE_PAID;

        $fiscalYear = $fiscalYear ?? config('payment.invoice_number.current_fiscal_year'); // TODO
        $affectedRecordCount = $this->invoiceNumberRepository->use($invoice, $type, $fiscalYear, $invoiceNumber);

        // No available InvoiceNumber, generate 100 available InvoiceNumbers
        // Normally this if-clause MUST NOT be executed otherwise something is wrong(race condition) and needs investigation
        if ($affectedRecordCount == 0) {
            info('Could not assign InvoiceNumber to invoice: ' . $invoice->id);
            $this->emergencyInvoiceNumberGenerator($type, $fiscalYear);

            // Try again to get an available InvoiceNumber
            $affectedRecordCount = $this->invoiceNumberRepository->use($invoice, $type, $fiscalYear);
            if ($affectedRecordCount == 0) {
                // TODO send critical error alert to sysAdmin / devTeam
            }
        }

        GenerateInvoiceNumberJob::dispatch($type, $fiscalYear);

        return $invoice->invoiceNumber;
    }

    public function emergencyInvoiceNumberGenerator(string $type, string $fiscalYear, int $count = 100): bool
    {
        $latestInvoiceNumber = $this->invoiceNumberRepository->getLatestInvoiceNumber($type, $fiscalYear);
        if (is_null($latestInvoiceNumber)) {
            $offset = match ($type) {
                InvoiceNumber::TYPE_PAID => config('payment.invoice_number.invoice_number_paid_offset', 1),
                InvoiceNumber::TYPE_REFUND => config('payment.invoice_number.invoice_number_refund_offset', 1),
            };
            $latestInvoiceNumber = $offset > 0 ? $offset - 1 : $offset;
        }
        $hundredAvailableInvoiceNumbers = [];
        $now = now();
        for ($i = 1; $i <= $count; $i++) {
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
        return $this->invoiceNumberRepository->insert($hundredAvailableInvoiceNumbers);
    }
}

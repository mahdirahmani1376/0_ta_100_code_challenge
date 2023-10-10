<?php

namespace App\Console\Commands;

use App\Models\InvoiceNumber;
use App\Repositories\Invoice\Interface\InvoiceNumberRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class GenerateInvoiceNumberCommand extends Command
{
    protected $signature = 'app:generate-invoice-number
                            {--type=paid : Type of InvoiceNumber to be generated, paid refund}
                            {--fiscal-year=1402 : Fiscal year of InvoiceNumbers}
                            {--count=100 : how many to generate}
                            {--lock-owner=null : Atomic lock owner, this is set only if this command is executed via queue}';

    protected $description = 'Generate Invoice Number records';

    public function handle(InvoiceNumberRepositoryInterface $invoiceNumberRepository)
    {
        $type = $this->option('type') ?? InvoiceNumber::TYPE_PAID;
        $fiscalYear = $this->option('fiscal-year') ?? config('payment.invoice_number.current_fiscal_year');
        $count = $this->option('count') ?? 100;

        $this->alert("Generating $count InvoiceNumber with type of $type and fiscalYear of $fiscalYear");

        $latestInvoiceNumber = $invoiceNumberRepository->getLatestInvoiceNumber($type, $fiscalYear);
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
        $success = $invoiceNumberRepository->insert($hundredAvailableInvoiceNumbers);

        if ($this->option('lock-owner')) {
            Cache::restoreLock('generateInvoiceNumberLock', $this->option('lock-owner'))->release();
        }

        if ($success) {
            $this->info("Generated $count InvoiceNumber with type of $type and fiscalYear of $fiscalYear");
        } else {
            $this->error('Failure');
        }
    }
}

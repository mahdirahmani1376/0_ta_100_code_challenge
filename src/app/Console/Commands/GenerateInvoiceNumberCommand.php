<?php

namespace App\Console\Commands;

use App\Integrations\MainApp\MainAppConfig;
use App\Models\InvoiceNumber;
use App\Services\Invoice\AssignInvoiceNumberService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class GenerateInvoiceNumberCommand extends Command
{
    protected $signature = 'app:generate-invoice-number
                            {--type=paid : Type of InvoiceNumber to be generated, paid refund}
                            {--count=100 : how many to generate}
                            {--lock-owner=null : Atomic lock owner, this is set only if this command is executed via queue}';

    protected $description = 'Generate Invoice Number records';

    public function handle(AssignInvoiceNumberService $assignInvoiceNumberService)
    {
        $this->info('Generating Invoice Numbers started');
        $type = $this->option('type') ?? InvoiceNumber::TYPE_PAID;
        $fiscalYear = MainAppConfig::get(MainAppConfig::INVOICE_NUMBER_CURRENT_FISCAL_YEAR);
        $count = $this->option('count') ?? 100;

        $this->alert("Generating $count InvoiceNumber with type of $type and fiscalYear of $fiscalYear");

        $success = $assignInvoiceNumberService->emergencyInvoiceNumberGenerator($type,$fiscalYear, $count);

        if ($this->option('lock-owner')) {
            Cache::restoreLock('generateInvoiceNumberLock', $this->option('lock-owner'))->release();
        }

        if ($success) {
            $this->info("Generated $count InvoiceNumber with type of $type and fiscalYear of $fiscalYear");
        } else {
            $this->warn('Generating Invoice Numbers failed');
        }
    }
}

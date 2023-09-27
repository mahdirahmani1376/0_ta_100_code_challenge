<?php

namespace App\Console\Commands;

use App\Actions\Invoice\CancelInvoiceAction;
use App\Helpers\JalaliCalender;
use App\Integrations\MainApp\MainAppAPIService;
use App\Integrations\MainApp\MainAppConfig;
use App\Models\Invoice;
use App\Models\Item;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Throwable;

class CancelOverDueInvoiceCommand extends Command
{
    protected $signature = 'cron:overdue-invoice
                            {--test : Run in test mode, will not commit anything into DB}
                            {--threshold=0 : Threshold for how old an Invoice should be in day}
                            {--threshold-domain=0 : Threshold for how old a Domain Invoice should be in day}';

    protected $description = 'Cancel overdue Invoices';

    private int $threshold = 0;
    private int $thresholdDomain = 0;
    private Carbon $dueDate;
    private Carbon $dueDateDomain;
    private CancelInvoiceAction $cancelInvoiceAction;
    private InvoiceRepositoryInterface $invoiceRepository;
    private bool $test;

    public function handle(CancelInvoiceAction $cancelInvoiceAction, InvoiceRepositoryInterface $invoiceRepository)
    {
        $this->cancelInvoiceAction = $cancelInvoiceAction;
        $this->invoiceRepository = $invoiceRepository;

        $this->test = !empty($this->option('test'));
        if ($this->test) {
            $this->info('TEST MODE ACTIVE');
        }

        App::setLocale('fa');
        $this->alert('Cancelling overdue invoices , now: ' . JalaliCalender::getJalaliString(now()) . '  ' . now()->toDateTimeString());

        $this->prepareThresholdValues();

        $this->cancelUnpaidInvoices();
        $this->cancelUnpaidDomainInvoices();
        $this->cancelMassPaymentInvoices();

        $this->newLine(2);
        $this->info('Completed');
    }

    private function cancelUnpaidInvoices()
    {
        $this->info('-------- Canceling every Invoice that is not a DomainService ------');

        $this->dueDate = now()
            ->second(0)
            ->minute(0)
            ->subDays($this->threshold);
        $this->info('Due Date: ' . JalaliCalender::getJalaliString($this->dueDate) . ' ' . $this->dueDate->format('H:i:s'));

        $overDueInvoices = $this->invoiceRepository->newQuery()
            ->where('status', Invoice::STATUS_UNPAID)
            ->where('is_mass_payment', 0)
            ->where('is_credit', 0)
            ->whereDate('due_date', '<', $this->dueDate)
            ->whereHas('items', function ($query) {
                $query->where('invoiceable_type', '<>', Item::TYPE_DOMAIN_SERVICE);
            })
            ->get();

        $this->cancelInvoices($overDueInvoices);
    }

    private function cancelUnpaidDomainInvoices()
    {
        $this->info('-------- Canceling every Invoice that is a DomainService ------');

        $this->dueDateDomain = now()
            ->second(0)
            ->minute(0)
            ->subDays($this->thresholdDomain);
        $this->info('Due Date Domain: ' . JalaliCalender::getJalaliString($this->dueDateDomain) . ' ' . $this->dueDateDomain->format('H:i:s'));

        $overDueInvoices = $this->invoiceRepository->newQuery()
            ->where('status', Invoice::STATUS_UNPAID)
            ->where('is_mass_payment', 0)
            ->where('is_credit', 0)
            ->whereDate('due_date', '<', $this->dueDateDomain)
            ->whereHas('items', function ($query) {
                $query->where('invoiceable_type', '=', Item::TYPE_DOMAIN_SERVICE);
            })
            ->get();

        $this->cancelInvoices($overDueInvoices);
    }

    private function cancelMassPaymentInvoices()
    {
        $this->info('-------- Canceling Mass Payment invoices ------');

        $this->info('Due Date Domain: ' . JalaliCalender::getJalaliString($this->dueDateDomain) . ' ' . $this->dueDateDomain->format('H:i:s'));

        $overDueInvoices = $this->invoiceRepository->newQuery()
            ->where('status', Invoice::STATUS_UNPAID)
            ->where('is_mass_payment', 1)
            ->where('is_credit', 0)
            ->whereDate('created_at', '<', now()->startOfDay()->format('Y-m-d H:i:s'))
            ->get();

        $success = 0;
        $overDueInvoices->each(function (Invoice $invoice) use (&$success) {
            try {
                if (!$this->test) {
                    ($this->cancelInvoiceAction)($invoice);
                }
                $this->info("Mass Invoice #{$invoice->getKey()} cancelled successfully.");
                $success++;
            } catch (Throwable $exception) {
                $this->error("Cron Cancel Mass Invoice #{$invoice->getKey()} Failed, {$exception->getMessage()}");
            }
        });
        $this->info("Mass Invoice Cancelled: " . $success);
    }

    private function prepareThresholdValues(): void
    {
        try {
            if (empty($this->option('threshold'))) {
                $this->info("No 'threshold' argument provided, fetching from MainApp configs...");
                $this->threshold = MainAppConfig::get('CRON_AUTO_INVOICE_CANCELLATION_DAYS');
                $this->info("'threshold' config received from MainApp: " . $this->threshold);
            } else {
                $this->threshold = $this->option('threshold');
            }

            if (empty($this->option('threshold-domain'))) {
                $this->info("No 'threshold-domain' argument provided, fetching from MainApp configs...");
                $this->thresholdDomain = MainAppConfig::get('CRON_AUTO_DOMAIN_INVOICE_CANCELLATION_DAYS');
                $this->info("'threshold-Domain' value received from MainApp: " . $this->thresholdDomain);
            } else {
                $this->thresholdDomain = $this->option('threshold-domain');
            }
        } catch (\Exception $exception) {
            \Log::error('Failed to fetch config values from MainApp', [
                'class' => self::class,
                'message' => $exception->getMessage(),
            ]);
            $this->error('Failed to fetch config values from MainApp');

            exit(-1);
        }
    }

    public function cancelInvoices(Collection $overDueInvoices): void
    {
        $this->info("Total Invoices: " . $overDueInvoices->count());

        $success = 0;
        $errors = 0;
        $overDueInvoices->each(function (Invoice $invoice) use (&$errors, &$success) {
            try {
                if (!$this->test) {
                    ($this->cancelInvoiceAction)($invoice);
                }
                $success++;
                Log::info("Cron Cancel Invoice #{$invoice->getKey()} cancelled successfully.");
                $this->info("[$success] Cron Cancel Invoice #{$invoice->getKey()} cancelled successfully.");
            } catch (Throwable $exception) {
                $errors++;
                Log::error("Cron Cancel Invoice #{$invoice->getKey()} Failed, {$exception->getMessage()}", $exception->getTrace());
                $this->error("[$errors] Cron Cancel Invoice #{$invoice->getKey()} Failed, {$exception->getMessage()}");
            }
        });
        $this->info("Successfully cancelled invoices: " . $success);
        $this->info("Failed cancelled invoices: " . $errors);
    }
}

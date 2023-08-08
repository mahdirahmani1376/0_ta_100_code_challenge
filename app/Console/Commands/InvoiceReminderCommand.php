<?php

namespace App\Console\Commands;

use App\Helpers\JalaliCalender;
use App\Integrations\MainApp\MainAppService;
use App\Models\Invoice;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;

class InvoiceReminderCommand extends Command
{
    protected $signature = 'cron:invoice-reminder
                            {--test : Run in test mode}
                            {--threshold1=0 : First reminder threshold in days}
                            {--threshold2=0 : Second reminder threshold in days}';

    protected $description = 'Send reminder notification to clients for their Invoices to pay them before due date';
    private bool $test;
    private InvoiceRepositoryInterface $invoiceRepository;
    private int $threshold1;
    private int $threshold2;

    public function handle(InvoiceRepositoryInterface $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;

        $this->test = !empty($this->option('test'));
        if ($this->test) {
            $this->info('TEST MODE ACTIVE');
        }

        App::setLocale('fa');
        $this->alert('Invoice reminder , now: ' . JalaliCalender::getJalaliString(now()) . '  ' . now()->toDateTimeString());

        $this->prepareThresholdValues();
        $this->sendReminder();

        $this->newLine(2);
        $this->info('Completed');
    }

    private function prepareThresholdValues(): void
    {
        try {
            if (empty($this->option('threshold1'))) {
                $this->info("No 'threshold1' argument provided, fetching from MainApp configs...");
                $this->threshold1 = MainAppService::getConfig('CRON_FINANCE_INVOICE_REMINDER_DAYS_1');
                $this->info("'threshold1' config received from MainApp: " . $this->threshold1);
            } else {
                $this->threshold1 = $this->option('threshold1');
            }

            if (empty($this->option('threshold2'))) {
                $this->info("No 'threshold2' argument provided, fetching from MainApp configs...");
                $this->threshold2 = MainAppService::getConfig('CRON_FINANCE_INVOICE_REMINDER_DAYS_2');
                $this->info("'threshold2' value received from MainApp: " . $this->threshold2);
            } else {
                $this->threshold2 = $this->option('threshold2');
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

    private function sendReminder()
    {
        $invoices = $this->invoiceRepository->newQuery()
            ->where('status', Invoice::STATUS_UNPAID)
            ->where(function (Builder $query) {
                $query->whereDate('due_date', '=', now()->subDays($this->threshold1)->toDateString());
                $query->orWhereDate('due_date', '=', now()->subDays($this->threshold2)->toDateString());
            })
            ->get();

        $this->info('Invoice count: ' . $invoices->count());

        $groupedByClient = $invoices->groupBy('client_id');
        $groupedByClient->each(function (Invoice $invoice) {
            if (!$this->test) {
                // todo send notification
            }

            $this->info("Invoice Reminder #{$invoice->getKey()} sent successfully.");
        });
    }
}

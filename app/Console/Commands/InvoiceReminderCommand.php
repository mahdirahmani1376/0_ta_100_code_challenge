<?php

namespace App\Console\Commands;

use App\Helpers\JalaliCalender;
use App\Integrations\MainApp\MainAppAPIService;
use App\Integrations\MainApp\MainAppConfig;
use App\Models\Invoice;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class InvoiceReminderCommand extends Command
{
    protected $signature = 'cron:invoice-reminder
                            {--test : Run in test mode}
                            {--email-threshold1=0 : First email reminder threshold in days}
                            {--email-threshold2=0 : Second email reminder threshold in days}
                            {--email-threshold3=0 : Third email reminder threshold in days}
                            {--sms-threshold1=0 : First sms reminder threshold in days}
                            {--sms-threshold2=0 : Second sms reminder threshold in days}';

    protected $description = 'Send notification to clients to remind them to pay their Invoices before due date';
    private bool $test;
    private InvoiceRepositoryInterface $invoiceRepository;

    private array $emailThresholds = [];
    private array $smsThresholds = [];

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
        $this->sendEmailReminder();
        $this->sendSMSReminder();

        $this->newLine(2);
        $this->info('Completed');
    }

    private function prepareThresholdValues(): void
    {
        try {
            // --------------- EMAIL ---------------------------
            if (empty($this->option('email-threshold1'))) {
                $this->info("No 'email-threshold1' argument provided, fetching from MainApp configs...");
                $this->emailThresholds[] = $t = MainAppConfig::get(MainAppConfig::CRON_FINANCE_INVOICE_REMINDER_EMAIL_1);
                $this->info("'email-threshold1' config received from MainApp: " . $t);
            } else {
                $this->emailThresholds[] = $this->option('email-threshold1');
            }

            if (empty($this->option('email-threshold2'))) {
                $this->info("No 'email-threshold2' argument provided, fetching from MainApp configs...");
                $this->emailThresholds[] = $t = MainAppConfig::get(MainAppConfig::CRON_FINANCE_INVOICE_REMINDER_EMAIL_2);
                $this->info("'email-threshold2' value received from MainApp: " . $t);
            } else {
                $this->emailThresholds[] = $this->option('email-threshold2');
            }

            if (empty($this->option('email-threshold3'))) {
                $this->info("No 'email-threshold3' argument provided, fetching from MainApp configs...");
                $this->emailThresholds[] = $t = MainAppConfig::get(MainAppConfig::CRON_FINANCE_INVOICE_REMINDER_EMAIL_3);
                $this->info("'email-threshold3' value received from MainApp: " . $t);
            } else {
                $this->emailThresholds[] = $this->option('email-threshold3');
            }
            // --------------- SMS ---------------------
            if (empty($this->option('sms-threshold1'))) {
                $this->info("No 'sms-threshold1' argument provided, fetching from MainApp configs...");
                $this->smsThresholds[] = $t = MainAppConfig::get(MainAppConfig::CRON_FINANCE_INVOICE_REMINDER_SMS_1);
                $this->info("'sms-threshold1' value received from MainApp: " . $t);
            } else {
                $this->smsThresholds[] = $this->option('sms-threshold1');
            }
            if (empty($this->option('sms-threshold2'))) {
                $this->info("No 'sms-threshold2' argument provided, fetching from MainApp configs...");
                $this->smsThresholds[] = $t = MainAppConfig::get(MainAppConfig::CRON_FINANCE_INVOICE_REMINDER_SMS_2);
                $this->info("'sms-threshold2' value received from MainApp: " . $t);
            } else {
                $this->smsThresholds[] = $this->option('sms-threshold2');
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

    private function sendEmailReminder()
    {
        foreach ($this->emailThresholds as $emailThreshold) {
            $invoices = $this->invoiceRepository->newQuery()
                ->where('status', Invoice::STATUS_UNPAID)
                ->whereDate('due_date', '=', now()->addDays($emailThreshold)->toDateString())
                ->get(['id', 'client_id']);

            $this->info('Invoice count: ' . $invoices->count());

            if ($invoices->count() == 0) {
                $this->info('No Invoices found.');

                continue;
            }

            $reminders = [];
            $invoices->groupBy('client_id')
                ->each(function ($item, $index) use (&$reminders) {
                    $reminders[] = [
                        'client_id' => $index,
                        'invoice_ids' => $item->pluck('id')->toArray(),
                    ];

                    $this->info("Email Invoice Reminder for client #$index sent successfully.");
                });

            if (!$this->test) {
                MainAppAPIService::sendInvoiceReminder($reminders, 'email');
            }
        }
    }

    private function sendSMSReminder()
    {
        foreach ($this->smsThresholds as $smsThreshold) {
            $invoices = $this->invoiceRepository->newQuery()
                ->where('status', Invoice::STATUS_UNPAID)
                ->whereDate('due_date', '=', now()->addDays($smsThreshold)->toDateString())
                ->get(['id', 'client_id']);

            $this->info('Invoice count: ' . $invoices->count());

            if ($invoices->count() == 0) {
                $this->info('No Invoices found.');

                continue;
            }

            $reminders = [];
            $invoices->groupBy('client_id')
                ->each(function ($item, $index) use (&$reminders) {
                    $reminders[] = [
                        'client_id' => $index,
                        'invoice_ids' => $item->pluck('id')->toArray(),
                    ];

                    $this->info("SMS Invoice Reminder for client #$index sent successfully.");
                });

            if (!$this->test) {
                MainAppAPIService::sendInvoiceReminder($reminders, 'sms');
            }
        }
    }
}

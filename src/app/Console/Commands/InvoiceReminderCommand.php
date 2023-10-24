<?php

namespace App\Console\Commands;

use App\Helpers\JalaliCalender;
use App\Integrations\MainApp\MainAppAPIService;
use App\Integrations\MainApp\MainAppConfig;
use App\Jobs\SendInvoiceReminder;
use App\Models\Invoice;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Bus;

class InvoiceReminderCommand extends Command
{
    protected $signature = 'cron:invoice-reminder
                            {--test : Run in test mode}
                            {--email-thresholds= : Email reminder thresholds in days e.g. 1,5,10}
                            {--sms-thresholds= : SMS reminder thresholds in days e.g. 5,15}';

    protected $description = 'Send notification to clients to remind them to pay their Invoices before due date';
    private bool $test;
    private InvoiceRepositoryInterface $invoiceRepository;
    private string $emailThresholds;
    private string $smsThresholds;
    private string $emailMessageTemplate;
    private string $emailLinkTemplate;
    private string $emailSubject;
    private string $smsMessageTemplate;
    private string $smsLinkTemplate;
    private array $reminders;

    public function handle(InvoiceRepositoryInterface $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;

        $this->test = !empty($this->option('test'));
        if ($this->test) {
            $this->info('TEST MODE ACTIVE');
        }

        App::setLocale('fa');
        $this->alert('Invoice reminder , now: ' . JalaliCalender::getJalaliString(now()) . '  ' . now()->toDateTimeString());

        $this->prepareConfigVariablesAndData();
        $this->sendEmailReminder();
        $this->sendSMSReminder();

        if (!empty($this->reminders)) {
            Bus::batch($this->reminders)->dispatch();
        }

        $this->newLine(2);
        $this->info('Completed');
    }

    private function prepareConfigVariablesAndData(): void
    {
        $hour = MainAppConfig::get(MainAppConfig::CRON_FINANCE_INVOICE_REMINDER_HOUR, noCache: true);
        if (now()->hour != $hour) {
            $this->info('Hour miss match now: ' . now()->hour . ' config: ' . $hour . ' , existing.');
            exit();
        }
        try {
            // --------------- EMAIL ---------------------------
            $this->emailMessageTemplate = MainAppConfig::get(MainAppConfig::CRON_FINANCE_INVOICE_REMINDER_EMAIL_MESSAGE);
            $this->emailLinkTemplate = MainAppConfig::get(MainAppConfig::CRON_FINANCE_INVOICE_REMINDER_EMAIL_LINK);
            $this->emailSubject = MainAppConfig::get(MainAppConfig::CRON_FINANCE_INVOICE_REMINDER_EMAIL_SUBJECT);
            if (empty($this->option('email-thresholds'))) {
                $this->info("No 'email-thresholds' argument provided, fetching from MainApp configs...");
                $this->emailThresholds = MainAppConfig::get(MainAppConfig::CRON_FINANCE_INVOICE_REMINDER_EMAIL);
                $this->info("'email-thresholds' config received from MainApp: " . $this->emailThresholds);
            } else {
                $this->emailThresholds = $this->option('email-thresholds');
            }

            // --------------- SMS ---------------------
            $this->smsMessageTemplate = MainAppConfig::get(MainAppConfig::CRON_FINANCE_INVOICE_REMINDER_SMS_MESSAGE);
            $this->smsLinkTemplate = MainAppConfig::get(MainAppConfig::CRON_FINANCE_INVOICE_REMINDER_SMS_LINK);
            if (empty($this->option('sms-thresholds'))) {
                $this->info("No 'sms-thresholds' argument provided, fetching from MainApp configs...");
                $this->smsThresholds = MainAppConfig::get(MainAppConfig::CRON_FINANCE_INVOICE_REMINDER_SMS);
                $this->info("'sms-thresholds' value received from MainApp: " . $this->smsThresholds);
            } else {
                $this->smsThresholds = $this->option('sms-thresholds');
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
        foreach (explode(',', $this->emailThresholds) as $emailThreshold) {
            if (empty($emailThreshold)) {
                continue;
            }
            $this->info('----- Email threshold: ' . $emailThreshold);
            $invoices = $this->prepareInvoices($emailThreshold);

            $this->info('Invoice count: ' . $invoices->count());

            if ($invoices->count() == 0) {
                $this->info('No Invoices found.');

                continue;
            }

            $invoices->groupBy('client_id')
                ->each(function ($invoices, int $clientId) {
                    try {
                        $payload = [
                            'reminders' => [
                                [
                                    'client_id' => $clientId,
                                    'message' => $this->prepareMessage($clientId, $invoices->pluck('id')->toArray(), $this->emailMessageTemplate, $this->emailLinkTemplate),
                                ]
                            ],
                            'subject' => $this->emailSubject,
                        ];

                        if (!$this->test) {
                            $this->reminders[] = new SendInvoiceReminder($payload, 'email');
                        }
                        $this->info("Email reminder for client #$clientId sent successfully.");
                    } catch (\Exception $e) {
                        $this->error("Email reminder for client #$clientId failed to process");
                    }
                });
        }
    }

    private function sendSMSReminder()
    {
        foreach (explode(',', $this->smsThresholds) as $smsThreshold) {
            $this->info('----- SMS threshold: ' . $smsThreshold);
            $invoices = $this->prepareInvoices($smsThreshold);

            $this->info('Invoice count: ' . $invoices->count());

            if ($invoices->count() == 0) {
                $this->info('No Invoices found.');

                continue;
            }

            $invoices->groupBy('client_id')
                ->each(function ($invoices, $clientId) {
                    try {
                        $payload = [
                            'reminders' => [
                                [
                                    'client_id' => $clientId,
                                    'message' => $this->prepareMessage($clientId, $invoices->pluck('id')->toArray(), $this->smsMessageTemplate, $this->smsLinkTemplate),
                                ],
                            ]
                        ];
                        if (!$this->test) {
                            $this->reminders[] = new SendInvoiceReminder($payload, 'sms');
                        }
                        $this->info("SMS reminder for client #$clientId sent successfully.");
                    } catch (\Exception $e) {
                        $this->error("SMS reminder for client #$clientId failed to process");
                    }
                });
        }
    }

    function prepareMessage(int $clientId, array $invoiceIds, string $messageTemplate, string $linkTemplate): string
    {
        $client = MainAppAPIService::getClients($clientId)[0];
        $message = parse_string($messageTemplate, [
            'client_name' => $client->full_name,
        ]);
        foreach ($invoiceIds as $invoiceId) {
            $message .= parse_string($linkTemplate, [
                'invoice_id' => $invoiceId,
            ]);
        }

        return $message;
    }

    public function prepareInvoices(string $emailThreshold): array|\Illuminate\Database\Eloquent\Collection
    {
        return $this->invoiceRepository->newQuery()
            ->where('status', Invoice::STATUS_UNPAID)
            ->whereDate('due_date', '=', now()->addDays($emailThreshold)->toDateString())
            ->get(['id', 'client_id']);
    }
}

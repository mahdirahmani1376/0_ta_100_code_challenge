<?php

namespace App\Console\Commands;

use App\Helpers\JalaliCalender;
use App\Integrations\MainApp\MainAppAPIService;
use App\Integrations\MainApp\MainAppConfig;
use App\Jobs\SendInvoiceReminderJob;
use App\Models\Invoice;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Bus;

class InvoiceReminderCommand extends Command
{
    protected $signature = 'cron:invoice-reminder
                            {--test : Run in test mode}
                            {--email-thresholds= : Email reminder thresholds in days e.g. 1,5,10}
                            {--sms-thresholds= : SMS reminder thresholds in days e.g. 5,15}
                            {--override-invoice-id= : Set this to an Invoice Id if you want to send an Email reminder}';

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
    private ?int $overrideInvoiceId;

    public function handle(InvoiceRepositoryInterface $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;

        $this->test = !empty($this->option('test'));
        if ($this->test) {
            $this->info('TEST MODE ACTIVE');
        }

        App::setLocale('fa');
        $this->alert('Invoice reminder , now: ' . JalaliCalender::getJalaliString(now()) . '  ' . now()->toDateTimeString());

        $this->overrideInvoiceId = $this->option('override-invoice-id');

        $this->prepareConfigVariablesAndData();
        $this->sendEmailReminder();
        if (empty($this->overrideInvoiceId)) {
            $this->sendSMSReminder();
        }

        if (!empty($this->reminders)) {
            Bus::batch($this->reminders)->dispatch();
        }

        $this->newLine(2);
        $this->info('Completed');
    }

    private function prepareConfigVariablesAndData(): void
    {
        $hour = MainAppConfig::get(key: MainAppConfig::CRON_FINANCE_INVOICE_REMINDER_HOUR, refresh: true);
        if (now()->hour != $hour && empty($this->overrideInvoiceId)) {
            $this->info('Hour miss match now: ' . now()->hour . ' config: ' . $hour);
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
        } catch (Exception $exception) {
            $this->error('Failed to fetch config values from MainApp');
        }
    }

    private function sendEmailReminder()
    {
        if (!empty($this->overrideInvoiceId)) {
            $this->emailThresholds = '1'; // to make sure the foreach below only iterates one time
        }
        foreach (explode(',', $this->emailThresholds) as $emailThreshold) {
            if (empty($emailThreshold)) {
                continue;
            }
            $this->info('----- Email threshold: ' . $emailThreshold . ' days before due_date');
            $invoices = $this->prepareInvoices($emailThreshold);

            $this->info('Invoice count: ' . $invoices->count());

            if ($invoices->count() == 0) {
                $this->info('No Invoices found.');

                continue;
            }

            $invoices->groupBy('profile_id')
                ->each(function ($invoices, int $clientId) {
                    try {
                        $payload = [
                            'reminders' => [
                                [
                                    'profile_id'  => $clientId,
                                    'message'     => $this->prepareMessage($clientId, $invoices->pluck('id')->toArray(), $this->emailMessageTemplate, $this->emailLinkTemplate),
                                    'invoice_ids' => $invoices->pluck('id')->toArray(),
                                ]
                            ],
                            'subject'   => $this->emailSubject,
                        ];

                        if (!$this->test) {
                            $this->reminders[] = new SendInvoiceReminderJob($payload, 'email');
                        }
                        $this->info("Email reminder for client #$clientId sent successfully.");
                    } catch (Exception $e) {
                        $this->error("Email reminder for client #$clientId failed to process");
                    }
                });
        }
    }

    private function sendSMSReminder()
    {
        foreach (explode(',', $this->smsThresholds) as $smsThreshold) {
            if (empty($smsThreshold)) {
                continue;
            }
            $this->info('----- SMS threshold: ' . $smsThreshold . ' days before due_date');
            $invoices = $this->prepareInvoices($smsThreshold);

            $this->info('Invoice count: ' . $invoices->count());

            if ($invoices->count() == 0) {
                $this->info('No Invoices found.');

                continue;
            }

            $invoices->groupBy('profile_id')
                ->each(function ($invoices, $clientId) {
                    try {
                        $payload = [
                            'reminders' => [
                                [
                                    'profile_id' => $clientId,
                                    'message'    => $this->prepareMessage($clientId, $invoices->pluck('id')->toArray(), $this->smsMessageTemplate, $this->smsLinkTemplate),
                                ],
                            ]
                        ];
                        if (!$this->test) {
                            $this->reminders[] = new SendInvoiceReminderJob($payload, 'sms');
                        }
                        $this->info("SMS reminder for client #$clientId sent successfully.");
                    } catch (Exception $e) {
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

    public function prepareInvoices($threshold): array|Collection
    {
        if (!empty($this->overrideInvoiceId)) {
            return $this->invoiceRepository->newQuery()
                ->where('id', $this->overrideInvoiceId)
                ->get(['id', 'profile_id']);
        }

        return $this->invoiceRepository->newQuery()
            ->where('status', Invoice::STATUS_UNPAID)
            ->where('is_mass_payment', false)
            ->whereNotNull('due_date')
            ->whereDate('due_date', now()->addDays($threshold)->toDateString())
            ->get(['id', 'profile_id']);
    }
}

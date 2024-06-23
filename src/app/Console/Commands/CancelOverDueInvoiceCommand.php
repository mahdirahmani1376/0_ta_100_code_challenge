<?php

namespace App\Console\Commands;

use App\Actions\Invoice\CancelInvoiceAction;
use App\Helpers\JalaliCalender;
use App\Models\Invoice;
use App\Models\Item;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Throwable;

class CancelOverDueInvoiceCommand extends Command
{
    protected $signature = 'cron:overdue-invoice
                            {--test : Run in test mode, will not commit anything into DB}';

    protected $description = 'Cancel overdue Invoices';
    private CancelInvoiceAction $cancelInvoiceAction;
    private InvoiceRepositoryInterface $invoiceRepository;
    private bool $test;
    private int $defaultThreshold = 10;
    private array $thresholds = [
        Item::TYPE_ADD_CLIENT_CREDIT       => 0,
        Item::TYPE_ADD_FUNDS               => 10,
        Item::TYPE_DOMAIN_SERVICE          => 10,
        Item::TYPE_PRODUCT_SERVICE         => 10,
        Item::TYPE_ADD_CLOUD_CREDIT        => 10,
        Item::TYPE_CLOUD                   => 10,
        Item::TYPE_ITEM                    => 10,
        Item::TYPE_PRODUCT_SERVICE_UPGRADE => 10,
        Item::TYPE_MASS_PAYMENT_INVOICE    => 10,
        Item::TYPE_ADMIN_TIME              => 10,
        Item::TYPE_CHANGE_SERVICE          => 10,
        Item::TYPE_PARTNER_DISCOUNT        => 10,
        Item::TYPE_PARTNER_COMMISSION      => 10,
        Item::TYPE_PARTNER_PAYMENT         => 10,
        Item::TYPE_AFFILIATION             => 10,
    ];

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

        $this->cancelUnpaidInvoices();

        $this->info('Completed');
    }

    private function cancelUnpaidInvoices(): void
    {
        $this->info('-------- Canceling every Invoice that is not a DomainService ------');

        foreach (Arr::sort($this->thresholds) as $itemType => $thresholdInDays) {
            $dueDate = now()
                ->second(0)
                ->minute(0)
                ->subDays($thresholdInDays);
            $this->info('Canceling Invoices with item type of: ' . $itemType);
            $this->info('Due Date: ' . JalaliCalender::getJalaliString($dueDate) . ' ' . $dueDate->format('H:i:s'));

            $overDueInvoices = $this->invoiceRepository->newQuery()
                ->where('status', Invoice::STATUS_UNPAID)
                ->where('is_credit', 0)
                ->whereDate('due_date', '<', $dueDate)
                ->whereHas('items', function ($query) use ($itemType) {
                    $query->where('invoiceable_type', $itemType);
                });

            $this->cancelInvoices($overDueInvoices);
        }
        $dueDate = now()
            ->second(0)
            ->minute(0)
            ->subDays($this->defaultThreshold);
        $this->info('Canceling Invoices with item type of: DEFAULT');
        $this->info('Due Date: ' . JalaliCalender::getJalaliString($dueDate) . ' ' . $dueDate->format('H:i:s'));

        $overDueInvoices = $this->invoiceRepository->newQuery()
            ->where('status', Invoice::STATUS_UNPAID)
            ->where('is_mass_payment', 0) // todo check this
            ->where('is_credit', 0) // todo check this
            ->whereDate('due_date', '<', $dueDate);

        $this->cancelInvoices($overDueInvoices);
    }

    public function cancelInvoices(Builder $overDueInvoices): void
    {
        $this->info("Invoices to cancel count: " . $overDueInvoices->count());

        $success = 0;
        $errors = 0;
        foreach ($overDueInvoices->cursor() as $invoice) {
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
        }
        $this->info("Successfully cancelled invoices: " . $success);
        $this->info("Failed cancelled invoices: " . $errors);
        $this->info('--------------------------------------');
        $this->newLine();
    }
}

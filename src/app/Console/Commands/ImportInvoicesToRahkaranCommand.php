<?php

namespace App\Console\Commands;

use App\Exceptions\SystemException\MaxDateIsOutOfRangeFiscalYearException;
use App\Exceptions\SystemException\MinDateIsOutOfRangeFiscalYearException;
use App\Helpers\JalaliCalender;
use App\Integrations\Rahkaran\RahkaranService;
use App\Models\ClientCashout;
use App\Models\Transaction;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Helper\ProgressBar;
use Throwable;

class ImportInvoicesToRahkaranCommand extends Command
{
    private $orginalDate = [];

    protected $signature = 'rahkaran:import-invoices
                    {--year=0 : Jalali Year}
                    {--month=0 : Jalali Month}
                    {--day=0 : Jalali Day}
                    {--days=1 : Days}
                    {--max-rounding-amount= : Max Rounding Amount}';


    protected $description = 'Imports invoices to rahkaran';

    private RahkaranService $rahkaranService;

    public function handle(): int
    {
        $this->rahkaranService = app(RahkaranService::class);
        $this->info('Date Time ' . Carbon::now()->toString());
        $this->info('Importing invoices to rahkaran...');

        $max_rounding_amount = $this->option('max-rounding-amount') ?? 10;

        [$this->fromDate, $this->toDate] = JalaliCalender::getRange(
            $this->option('year'),
            $this->option('month'),
            $this->option('day'),
            'custom',
            true,
            $this->option('days') > -1 ? $this->option('days') : null
        );

        $this->orginalDate = [clone $this->fromDate, clone $this->toDate];

        App::setLocale('fa');

        $this->checkFiscalYear();

        Log::info(
            "ImportInvoicesToRahkaranCommand Invoices from {JalaliCalender::getJalaliString($this->fromDate)} to {JalaliCalender::getJalaliString($this->toDate)}",
            [
                'year' => $this->option('year'),
                'month' => $this->option('month'),
                'day' => $this->option('day'),
                'days' => $this->option('days')
            ]
        );

        // Import Transactions In range
        $this->importTransactions();

        // Import Invoices In range
        $this->importInvoices($max_rounding_amount);

        // Import CashOuts
        $this->importCashOuts();

        $this->info('Completed...');
        $this->info('Date Time ' . Carbon::now()->toString());
        return 1;
    }

    private function importTransactions()
    {
        $total_transactions = $this->getTransactionQuery()->count();
        Log::info("ImportInvoicesToRahkaranCommand Total Transactions: {$total_transactions}");

        $this->newLine(2);
        $this->info("Total Transactions: {$total_transactions}");
        $this->newLine();
        $bar = $this->output->createProgressBar($total_transactions);
        $bar->setFormat(ProgressBar::FORMAT_VERY_VERBOSE);

        $errors = 0;
        $index = 0;

        $this->rahkaranService->setBankGateways();

        /**
         * @var Transaction $transaction
         */
        foreach ($this->getTransactionQuery()->cursor() as $transaction) {
            $index++;
            try {
                $this->rahkaranService->createTransaction($transaction);
                Log::info("ImportInvoicesToRahkaranCommand Transaction #{$transaction->id} imported");
            } catch (Throwable $exception) {
                $errors++;
                $this->error($exception->getMessage());
                Log::error("ImportInvoicesToRahkaranCommand Transaction #{$transaction->id} failed, {$exception->getMessage()}");
            }

            $bar->advance();
        }

        $this->newLine();
        $this->info("Imported transactions: {$index}");
        $this->info("Failed transactions: {$errors}");
    }

    private function importInvoices($max_rounding_amount = 10)
    {
        $total_invoices = $this->getInvoicesQuery()->count();
        $this->newLine(2);
        $this->info("Total Invoices: {$total_invoices}");
        Log::info("ImportInvoicesToRahkaranCommand Total Invoices: {$total_invoices}");

        $this->newLine();
        $bar = $this->output->createProgressBar($total_invoices);
        $bar->setFormat(ProgressBar::FORMAT_VERY_VERBOSE);

        for ($i = 0; $this->fromDate < $this->toDate; $i++) {
            $invoices = $this->getInvoicesQuery($this->fromDate);
            $count = $invoices->count();
            $this->info('Create voucher for ' . $this->fromDate->format('Y-m-d') . ' Invoice count: ' . $count);

            if ($count == 0) {
                $this->fromDate->addDay();
                continue;
            }

	    $matched = $invoices->get();
            $jalali_date = JalaliCalender::carbonToJalali($matched[0]->paid_at ?? $matched[0]->created_at);

            $this->rahkaranService->createBulkInvoice($matched, $max_rounding_amount, $jalali_date);
            Log::info("ImportInvoicesToRahkaranCommand Invoices on {$jalali_date} imported successfully");

            $this->fromDate->addDay();
        }
    }

    private function importCashOuts()
    {
        $cashOuts = $this->getCashoutsQuery();
        $this->info('Add Voucher of Zarinpal payments fee, Count of items ' . $cashOuts->count());
        $ask = $this->ask('Import items?[y for yes]');

        if ($cashOuts->count() > 0 && $ask == 'y') {
            $jalali_date = JalaliCalender::carbonToJalali($this->orginalDate[1]);
            $this->rahkaranService->createZarinpalPaymentsFee($cashOuts->get(), $jalali_date);
        } else {
            $this->warn('Import cash outs skipped!');
        }
    }

    /**
     * Gets invoice query
     *
     * Gets only Paid (Paid|Collection) or Refunded Invoices
     * Imported invoices are excluded
     *
     * @param null $date
     * @return Builder
     */
    protected function getInvoicesQuery($date = null): Builder
    {

        $from = !empty($date) ? $date->format('Y-m-d') . ' 00:00:00' : $this->fromDate;
        $to = !empty($date) ? $date->format('Y-m-d') . ' 23:59:59' : $this->toDate;

        /** @var InvoiceRepositoryInterface $invoiceRepository */
        $invoiceRepository = app(InvoiceRepositoryInterface::class);

        return $invoiceRepository->rahkaranQuery($from, $to);
    }

    /**
     * @return Builder
     */
    private function getCashoutsQuery(): Builder
    {
        return ClientCashout::query()
            ->where('updated_at', '>=', $this->orginalDate[0])
            ->where('updated_at', '<=', $this->orginalDate[1])
            ->where('status', ClientCashout::STATUS_ACTIVE)
            ->orderByDesc('updated_at');
    }

    /**
     * Gets transaction query
     *
     * Gets only successful external transactions
     * Credit transactions, Rounding transactions and Imported transactions are excluded
     * Rounding transactions can be imported with their Paid or Refunded invoice
     *
     * @return Builder
     */
    protected function getTransactionQuery(): Builder
    {
        /** @var TransactionRepositoryInterface $transactionRepository */
        $transactionRepository = app(TransactionRepositoryInterface::class);

        return $transactionRepository->rahkaranQuery($this->fromDate, $this->toDate);
    }

    /**
     * @throws MinDateIsOutOfRangeFiscalYearException
     * @throws MaxDateIsOutOfRangeFiscalYearException
     */
    protected function checkFiscalYear()
    {
        $fiscal_year = $this->option('year') ?? config('payment.invoice_number.current_fiscal_year'); // TODO

        $this->info("Current Fiscal Year: {$fiscal_year}");

        $min_date = JalaliCalender::makeCarbonByJalali(
            $fiscal_year,
            1,
            1,
        )->hour(0)->minute(0)->second(0);

        $max_date = JalaliCalender::makeCarbonByJalali(
            $fiscal_year + 1,
            1,
            1,
        )->hour(0)->minute(0)->second(0)->subSecond();

        $this->table(
            [
                'Invoice Date',
                '',
                '',
                'Time'
            ],
            [
                [
                    'Selected From',
                    $this->fromDate->format('Y-m-d'),
                    JalaliCalender::getJalaliString($this->fromDate),
                    $this->fromDate->format('H:i:s')
                ],
                [
                    'Selected To',
                    $this->toDate->format('Y-m-d'),
                    JalaliCalender::getJalaliString($this->toDate),
                    $this->toDate->format('H:i:s')
                ],
                [''],
                [
                    'Fiscal Min Date',
                    $min_date->format('Y-m-d'),
                    JalaliCalender::getJalaliString($min_date),
                    $min_date->format('H:i:s')
                ],
                [
                    'Fiscal Max To',
                    $max_date->format('Y-m-d'),
                    JalaliCalender::getJalaliString($max_date),
                    $max_date->format('H:i:s')
                ],
            ]
        );

        if (
            $this->fromDate->isBefore(
                $min_date->clone()->subSecond()
            )
        ) {
            throw MinDateIsOutOfRangeFiscalYearException::make(
                JalaliCalender::getJalaliString($this->fromDate),
                JalaliCalender::getJalaliString($min_date)
            );
        }

        if (
            $this->toDate->greaterThanOrEqualTo(
                $max_date
            )
        ) {
            throw MaxDateIsOutOfRangeFiscalYearException::make(
                JalaliCalender::getJalaliString($this->toDate),
                JalaliCalender::getJalaliString($max_date),
            );
        }
    }
}

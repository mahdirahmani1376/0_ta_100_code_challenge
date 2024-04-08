<?php

namespace App\Console\Commands;

use App\Integrations\Moadian\MoadianService;
use App\Models\Invoice;
use App\Models\MoadianLog;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Jooyeshgar\Moadian\Facades\Moadian;

class ImportInvoiceToMoadianCommand extends Command
{
    protected $signature = 'moadian:import {--from=} {--to=} {--override-id=} {--debug=false} {--check=}';

    protected $description = 'This command will import income Invoices into Moadian';

    private bool $debug = false;

    public function handle(): int
    {
        if (empty($this->option('override-id'))) {
            if (empty($this->option('from')) || empty($this->option('to'))) {
                $this->error('From Date / To Date inputs are required!');
                return 1;
            }

            $from = Carbon::parse($this->option('from'))->format('Y-m-d 00:00:00');
            $to = Carbon::parse($this->option('to'))->format('Y-m-d 23:59:59');
        } else {
            $from = null;
            $to = null;
        }

        if ($check = $this->option('check')) {
            dd(Moadian::inquiryByReferenceNumbers($check)->getBody());
            return 0;
        }

        $invoices = $this->getInvoicesQuery($from, $to);

        $this->alert("Importing Invoices from: $from - to: $to");
        $this->info('Invoices Found #' . $invoices->count());

        $this->debug = $this->option('debug') == "true" || !$this->option('debug');
        if (!$this->debug) {
            $this->error('!!! Running In Production Mode !!!');
            sleep(2);
        }

        /** @var Invoice $invoice */
        foreach ($invoices->cursor() as $invoice) {
            $this->info('Preparing invoice: ' . $invoice->invoice_id);
            // TODO maybe sleep or something to prevent sending too many requests to the moadian server
            // TODO or maybe queue all of these sendInvoices and chain em synchronously using
            // TODO https://laravel.com/docs/10.x/queues#dispatching-batches
            try {
                $this->sendInvoice($invoice);
                $this->info('Sent invoice: ' . $invoice->invoice_id);
            } catch (\Exception $exception) {
                $this->error($exception->getMessage());
                $this->info('skipping Invoice#' . $invoice->invoice_id);
            }
        }

        $this->info('Command Finished');
        return 0;
    }

    private function getInvoicesQuery($fromDate, $toDate): Builder
    {
        if (!empty($this->option('override-id'))) {
            $this->info('override id = ' . $this->option('override-id'));
            return Invoice::query()->where('invoice_id', $this->option('override-id'));
        }
        // query copied from ImportInvoicesToRahkaranCommand.php
        $query = Invoice::query()->where(function (Builder $query) use ($fromDate, $toDate) {
            $query->orWhere(function (Builder $status_query) use ($fromDate, $toDate) {
                $status_query->whereDate('created_at', '>=', $fromDate);
                $status_query->whereDate('created_at', '<=', $toDate);
                $status_query->where('status', Invoice::STATUS_COLLECTIONS);
            });
            $query->orWhere(function (Builder $status_query) use ($fromDate, $toDate) {
                $status_query->whereDate('paid_at', '>=', $fromDate);
                $status_query->whereDate('paid_at', '<=', $toDate);
            });
        });

        // Only paid or refunded invoices can be imported
        $query->whereIn('status', [
            Invoice::STATUS_PAID,
            Invoice::STATUS_COLLECTIONS,
        ]);

        $query->where('is_mass_payment', 0);
        $query->where('is_credit', 0);

        $query->where('tax', '>', 0);

        // Filters out imported invoices
        $query->where(function (Builder $query) {
            $query->whereDoesntHave('moadianLog');
            $query->orWhereHas('moadianLog', function (Builder $query) {
                $query->where('status', MoadianLog::STATUS_FAILURE);
            });
        });

        return $query;
    }

    public function sendInvoice(Invoice $invoice): void
    {
        if ($this->debug) {
            $this->info(json_encode(MoadianService::buildMoadianInvoice($invoice)->toArray()));
            return;
        }

        MoadianService::sendInvoice($invoice);
    }
}


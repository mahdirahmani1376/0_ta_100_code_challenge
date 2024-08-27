<?php

namespace App\Console\Commands;

use App\Actions\Invoice\ChangeInvoiceStatusAction;
use App\Models\Invoice;
use Illuminate\Console\Command;

class TempFixCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:temp-fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(ChangeInvoiceStatusAction $changeInvoiceStatusAction)
    {
        $this->info('Command Starts');

	$invoices = Invoice::query()->where('balance', 0)->where('total', '>', 0)->where('status', 'unpaid')->get();

	$this->info('Count of Invoices not paid status:' . $invoices->count());

	foreach( $invoices as $invoice )
	{
		$this->info('Mark as paid: ' . $invoice->id);
		($changeInvoiceStatusAction)($invoice, 'paid');
	}

	$invoices = Invoice::query()->where('balance', 0)->where('status', 'paid')->whereNotNull('processed_at')->whereNull('paid_at')->get();
	$this->info('Count of Invoices no paid_at:' . $invoices->count());

	foreach( $invoices as $invoice )
        {
                $this->info('Paid date filled: ' . $invoice->id);
		$invoice->paid_at = $invoice->processed_at;
		$invoice->save();
        }
    }
}

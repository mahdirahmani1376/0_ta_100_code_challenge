<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SyncInvoiceTaxRateCommand extends Command
{
    protected $signature = 'sync-invoice-tax';

    protected $description = 'Command description';

    public function handle()
    {
        $whmcs_tax_rates = [
            3, 9, 4, 5, 0, 6, 10, ""
        ];

        foreach ($whmcs_tax_rates as $rate) {
            $this->info('Start invoices with tax : ' . $rate);
            $invoices = DB::connection('whmcs')->table('tblinvoices')
                ->select(['id', 'taxrate'])->where('taxrate', 3)
                ->orderBy('id')
                ->chunk(10000, function (Collection $whmcs_invoices) use ($rate) {
                    $finance_invoices = DB::connection('mysql')->table('invoices')
                        ->whereIn('id', $whmcs_invoices->pluck('id'))
                        ->update([
                            'tax_rate' => $rate
                        ]);

                    $this->info("Update {$finance_invoices} rows successfully");
                });

        }
    }
}

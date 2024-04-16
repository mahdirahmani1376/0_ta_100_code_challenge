<?php

namespace App\Console\Commands;

use App\Actions\Invoice\Item\UpdateItemAction;
use App\Integrations\MainApp\MainAppAPIService;
use App\Models\Invoice;
use App\Models\Item;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class CheckInvoicePriceCommand extends Command
{
    protected $signature = 'invoice:check';
    protected $description = 'Update Unpaid Invoice Prices after 72 hours';

    public function __construct(
        public UpdateItemAction $updateItemAction
    )
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->info('getting all unpaid invoices older than 72 hours');

        $unpaidInvoices = Invoice::query()
            ->where('status', Invoice::STATUS_UNPAID)
            ->whereDate('updated_at', '<', now()->subHours(72))
            ->get();

        $this->info("count of unpaid invoices: {$unpaidInvoices->count()}");

        $unpaidInvoices->each(function (Invoice $invoice) {
            $invoice->items->each(function (Item $item) use ($invoice) {
                try {

                    if ($item->invoiceable_type == Item::TYPE_DOMAIN) {
                        $data = MainAppAPIService::recalculateDomainServicePrice($item->invoiceable_id);
                        ($this->updateItemAction)($invoice, $item, [
                            'amount' => $data['cost']
                        ]);
                    }

                    if ($item->invoiceable_type == Item::TYPE_PRODUCT_SERVICE) {
                        $data = MainAppAPIService::recalculateProductServicePrice($item->invoiceable_id);
                        ($this->updateItemAction)($invoice, $item, [
                            'amount' => $data['cost']
                        ]);
                    }


                    $this->info("Item: $item->id updated successfully for invoice:$invoice->id changes: " . json_encode($item->getChanges()));
                    Log::info("Item: $item->id updated successfully for invoice:$invoice->id", [
                        'changes'  => $item->getChanges(),
                    ]);


                } catch (Throwable $e) {
                    $this->error("item with id:$item->id with invoice $invoice->id threw exception with message {$e->getMessage()}");
                    Log::error("item with id:$item->id with invoice $invoice->id threw exception", [
                        'response' => $data,
                        'error' => $e->getMessage(),
                        'trace'    => $e->getTrace()
                    ]);
                }
            });
        });

        $this->info('Completed');

    }

}

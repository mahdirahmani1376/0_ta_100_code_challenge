<?php

namespace App\Console\Commands;

use App\Actions\Invoice\Item\UpdateItemAction;
use App\Integrations\MainApp\MainAppAPIService;
use App\Models\FinanceLog;
use App\Models\Invoice;
use App\Models\Item;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpdateInvoiceItemsCommand extends Command
{
    protected $signature = 'cron:invoice-update';
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
            $invoice->items
                ->whereIn('invoiceable_type', [Item::TYPE_DOMAIN,Item::TYPE_PRODUCT_SERVICE])
                    ->each(function (Item $item) use ($invoice) {
                        $this->updateItem($item, $invoice);
                    });
        });

        $this->info('Completed');

    }

    private function updateItem(Item $item, Invoice $invoice): void
    {
        try {
            $oldState = $invoice->toArray();

            if ($item->invoiceable_type == Item::TYPE_DOMAIN){
                $response = MainAppAPIService::recalculateDomainServicePrice($item->invoiceable_id);
                $price = $response;
            }
            elseif ($item->invoiceable_type == Item::TYPE_PRODUCT_SERVICE){

                $response = MainAppAPIService::recalculateProductServicePrice($item->invoiceable_id);
                $price = data_get($response, 'cost');
            }

            if (!empty($price) and !empty($response)){
                ($this->updateItemAction)($invoice, $item, [
                    'amount' => $price
                ]);

                finance_log(FinanceLog::EDIT_INVOICE_ITEM, $item, $item->getChanges(), $oldState, $response);

                $this->info("Item: $item->id updated successfully for invoice:$invoice->id changes: " . json_encode($item->getChanges()));
                Log::info("Item: $item->id updated successfully for invoice:$invoice->id", [
                    'changes' => $item->getChanges(),
                ]);
            }
        } catch (Throwable $e) {
            $this->error("item with id:$item->id with invoice $invoice->id threw exception with message {$e->getMessage()}");
            Log::error("item with id:$item->id with invoice $invoice->id threw exception", [
                'response' => $response,
                'error'    => $e->getMessage(),
                'trace'    => $e->getTrace()
            ]);
        }
    }

}

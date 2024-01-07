<?php

namespace App\Console\Commands;

use App\Integrations\Moadian\MoadianService;
use App\Models\MoadianLog;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class InquiryMoadianCommand extends Command
{
    protected $signature = 'moadian:inquiry {--invoice_id=} {--debug=false}';

    protected $description = 'This command will inquiry Invoices status from Moadian';

    private bool $debug = false;

    public function handle(): int
    {

        $this->debug = $this->option('debug') == "true" || !$this->option('debug');

        $moadianLogQuery = $this->getMoadianLogQuery();

        $this->info('Records Found #' . $moadianLogQuery->count());

        if ($this->debug) {
            $this->warn('Command running in debug mode.');
        }

        /** @var MoadianLog $moadianLog */
        foreach ($moadianLogQuery->cursor() as $moadianLog) {
            try {
                $this->info('Get info invoice id #' . $moadianLog->invoice_id);
                $this->inquiryInvoice($moadianLog);
            } catch (\Exception $exception) {
                $this->error($exception->getMessage());
            }
        }

        $this->info('Command Finished');
        return 0;
    }

    private function getMoadianLogQuery(): Builder
    {
        if (!empty($this->option('invoice_id'))) {
            $this->info('override id = ' . $this->option('invoice_id'));
            return MoadianLog::query()->where('invoice_id', $this->option('invoice_id'));
        }

        return MoadianLog::query()->where('status', MoadianLog::STATUS_PENDING);
    }

    public function inquiryInvoice(MoadianLog $moadianLog): void
    {
        if ($this->debug) {
            $this->info('DEBUG ==> ' . json_encode($moadianLog->toArray()));
            return;
        }

        $response = MoadianService::inquiryMoadian($moadianLog);

        if (!empty($this->option('invoice_id'))) {
            dump($response);
        }
        $this->info('received response for: ' . $response['referenceNumber'] . ' Status: ' . $response['status']);
    }
}

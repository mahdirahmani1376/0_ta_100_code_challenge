<?php

namespace App\Jobs;

use App\Repositories\Invoice\Interface\InvoiceNumberRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class GenerateInvoiceNumberJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly string $type, private readonly string $fiscalYear)
    {
    }

    public function handle(InvoiceNumberRepositoryInterface $invoiceNumberRepository): void
    {
        $unusedCount = $invoiceNumberRepository->countUnused($this->type, $this->fiscalYear);
        $threshold = 500; // TODO read this from config or something
        if ($unusedCount < $threshold) {
            $lock = Cache::lock('generateInvoiceNumberLock', 10);
            if ($lock->get()) {
                Artisan::call('app:generate-invoice-number', [
                    '--type' => $this->type,
                    '--fiscal-year' => $this->fiscalYear,
                    '--count' => $threshold,
                    '--lock-owner' => $lock->owner(),
                ]);
            }
        }
    }
}

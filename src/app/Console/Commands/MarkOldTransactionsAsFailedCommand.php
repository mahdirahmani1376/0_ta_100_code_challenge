<?php

namespace App\Console\Commands;

use App\Helpers\JalaliCalender;
use App\Models\Transaction;
use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class MarkOldTransactionsAsFailedCommand extends Command
{
    protected $signature = 'cron:mark-transactions-failed
                            {hourAgo}
                            {--test : Run in test mode, will not commit anything into DB}';

    protected $description = 'Mark pending Transactions as failed';

    public function handle(TransactionRepositoryInterface $transactionRepository)
    {

        $this->test = !empty($this->option('test'));
        if ($this->test) {
            $this->info('TEST MODE ACTIVE');
        }

        App::setLocale('fa');
        $this->alert('Marking old pending Transactions as failed, now: ' . JalaliCalender::getJalaliString(now()) . '  ' . now()->toDateTimeString());

        $threshold = Carbon::now()->subHours($this->argument('hourAgo'))->format('Y-m-d H:00:00');

        if ($this->test) {
            $count = $transactionRepository->newQuery()
                ->whereIn('status', [Transaction::STATUS_PENDING, Transaction::STATUS_PENDING_BANK_VERIFY])
                ->where('created_at', '<=', $threshold)
                ->count();
        } else {
            $count = $transactionRepository->newQuery()
                ->whereIn('status', [Transaction::STATUS_PENDING, Transaction::STATUS_PENDING_BANK_VERIFY])
                ->where('created_at', '<=', $threshold)
                ->update(['status' => Transaction::STATUS_FAIL]);
        }

        $this->info('Number of old Transactions: ' . $count);

        $this->info('Completed');
    }
}

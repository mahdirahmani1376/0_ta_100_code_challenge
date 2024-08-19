<?php

namespace App\Services\Report;

use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use App\Repositories\Transaction\TransactionRepository;
use Illuminate\Support\Carbon;

class FinanceHourlyReportService
{
    public function __construct(
        private readonly InvoiceRepositoryInterface $invoiceRepository,
        private readonly TransactionRepository      $transactionRepository,
    )
    {
    }

    public function __invoke($data)
    {
        $date1 = Carbon::now()->startOfDay();
        $date2 = Carbon::now()->startOfHour();

        if ($date1 == $date2) {
            $date1->subDay();
        }

        $date1 = $date1->format('Y-m-d H:i:s');
        $date2 = $date2->format('Y-m-d H:i:s');

        $content = "💰Income: (" . $date2 . ")\n\n";

        $todayIncome = $this->invoiceRepository->hourlyReport($date1, $date2);


        $date3 = Carbon::now()->subDay()->startOfDay();
        $date4 = Carbon::now()->subDay()->startOfHour();

        if ($date3 == $date4) {
            $date3->subDay();
        }

        $date3 = $date3->format('Y-m-d H:i:s');
        $date4 = $date4->format('Y-m-d H:i:s');

        $lastDayIncome = $this->invoiceRepository->hourlyReport($date3, $date4);


        $content = $content .
            "*" . number_format($todayIncome) . " IRR*" . $this->getDiff($todayIncome, $lastDayIncome)
            . "\n\n" . "Last Day :(" . $date3 . " - " . explode(' ', $date4)[1] . ")\n" .
            "" . number_format($lastDayIncome) . " IRR\n\n" .
            "Last day Total Income: " . "\n";

        $date1 = Carbon::now()->subDay();
        $date2 = Carbon::now()->subDay();

        $lastDayTotalIncome = $this->invoiceRepository->hourlyReport($date1->format('Y-m-d 00:00:00'), $date2->format('Y-m-d 23:59:59'));

        $report = $content . "*" . number_format($lastDayTotalIncome) . " IRR*";

        $totalBankInput = $this->transactionRepository->sumOfPaidTransactionsByCriteria(
            criteria: [
                'created_at' => now()
            ], onlinePayment: true
        );

        $bankIcon = "🏦";

        $totalBankInput = "\n\n$bankIcon Total Bank Input: \n\n*" . number_format($totalBankInput) . " IRR*";

        return $report . $totalBankInput;
    }

    private function getDiff($newIncome, $lastIncome)
    {
        if ($lastIncome == 0) {
            $percentage = 0;
        } else {
            $percentage = round((($newIncome - $lastIncome) / $lastIncome) * 100, 2);
        }

        if ($percentage >= 0) {
            return "✅" . $percentage . "%";
        }

        return "🔻" . $percentage . "%";
    }
}

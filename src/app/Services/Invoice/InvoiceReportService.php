<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;

class InvoiceReportService
{
    public function __construct(private readonly InvoiceRepositoryInterface $invoiceRepository)
    {
    }

    public function __invoke()
    {
        $date1 = now()->startOfDay();
        $date2 = now()->startOfHour();

        if ($date1 == $date2) {
            $date1->subDay();
        }

        $date1 = $date1->format('Y-m-d H:i:s');
        $date2 = $date2->format('Y-m-d H:i:s');

        $content = "💰Income: (" . $date2 . ")\n\n";

        $todayIncome = $this->invoiceRepository->newQuery()
            ->whereDate('paid_at', '>=', $date1)
            ->whereDate('paid_at', '<=', $date2)
            ->where('is_credit', 0)
            ->where('is_mass_payment', 0)
            ->where('status', Invoice::STATUS_PAID)
            ->sum('total');


        $date3 = now()->subDay()->startOfDay();
        $date4 = now()->subDay()->startOfHour();

        if ($date3 == $date4) {
            $date3->subDay();
        }

        $date3 = $date3->format('Y-m-d H:i:s');
        $date4 = $date4->format('Y-m-d H:i:s');

        $lastDayIncome = $this->invoiceRepository->newQuery()
            ->whereDate('paid_at', '>=', $date3)
            ->whereDate('paid_at', '<=', $date4)
            ->where('is_credit', 0)
            ->where('is_mass_payment', 0)
            ->where('status', Invoice::STATUS_PAID)
            ->sum('total');
        if ($lastDayIncome === 0) {
            $lastDayIncome = 1;
        }


        $percentage = round((($todayIncome - $lastDayIncome) / $lastDayIncome) * 100, 2);
        if ($percentage >= 0) {
            $todayIncomeDiff = "✅" . $percentage . "%";
        } else {
            $todayIncomeDiff = "🔻" . $percentage . "%";
        }

        $content = $content .
            "*" . number_format($todayIncome) . " IRR*" . $todayIncomeDiff
            . "\n\n" . "Last Day :(" . $date3 . " - " . explode(' ', $date4)[1] . ")\n" .
            "" . number_format($lastDayIncome) . " IRR\n\n" .
            "Last day Total Income: " . "\n";

        $date1 = now()->subDay();
        $date2 = now()->subDay();

        $lastDayTotalIncome = $this->invoiceRepository->newQuery()
            ->whereDate('paid_at', '>=', $date1->format('Y-m-d 00:00:00'))
            ->whereDate('paid_at', '<=', $date2->format('Y-m-d 23:59:59'))
            ->where('is_credit', 0)
            ->where('is_mass_payment', 0)
            ->where('status', Invoice::STATUS_PAID)
            ->sum('total');

        return $content . "*" . number_format($lastDayTotalIncome) . " IRR*";
    }
}

<?php

namespace App\Services\Admin\Invoice;

use App\Services\Excel\ExcelService;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Excel;
use Symfony\Component\HttpFoundation\File\File;

class GenerateInvoiceExcelFileService
{
    public function __invoke(Collection $invoices): File
    {
        $invoices->map(function ($invoice) {
            $invoice['income'] = !($invoice->is_credit || $invoice->is_mass_payment);
            return $invoice;
        });

        $sheet_name = "Invoices_" . now()->format("Y-m-d") . "_" . Str::random(6);
        $filename = $sheet_name . "." . Excel::XLSX;

        $file = (new ExcelService([
            [
                "rows" => $invoices,
                "columns" => [
                    trans("exportable.general.client_name") => "client.full_name",
                    trans("exportable.invoice.paid_date_jalali") => "paid_date:jalali",
                    trans("exportable.invoice.paid_date") => "paid_date",
                    trans("exportable.invoice.invoice_id") => "id",
                    trans("exportable.general.payment_method") => "payment_method",
                    trans("exportable.invoice.total") => "total",
                    trans("exportable.general.income") => "income"
                ],
                "title" => $sheet_name
            ]
        ]))->generateExcel()->download($filename)->getFile();

        return $file;
    }
}

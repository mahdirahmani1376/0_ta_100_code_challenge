<?php
namespace App\Services\Excel;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;


class MainSheetExporter implements WithMultipleSheets
{
    use Exportable;

    private array $sheets;

    public function __construct($sheets)
    {
        $this->sheets = $sheets;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->sheets as $sheet) {
            $sheets[] = new MainExporter($sheet);
        }

        return $sheets;
    }
}

<?php

namespace App\Services\Excel;

class ExcelService
{
    public MainSheetExporter $file;
    public array $sheets;

    public function __construct($sheets)
    {
        $this->sheets = $sheets;
    }

    public function generateExcel(): MainSheetExporter
    {
        return (new MainSheetExporter($this->sheets));
    }
}

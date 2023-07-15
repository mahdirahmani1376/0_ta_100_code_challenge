<?php

namespace App\Actions\Admin\Invoice;

use App\Services\Admin\Invoice\GenerateInvoiceExcelFileService;
use App\Services\Admin\Invoice\IndexInvoiceService;
use Illuminate\Support\Collection;

class IndexInvoiceAction
{
    private IndexInvoiceService $indexInvoiceService;
    private GenerateInvoiceExcelFileService $generateInvoiceExcelFileService;

    public function __construct(
        IndexInvoiceService             $indexInvoiceService,
        GenerateInvoiceExcelFileService $generateInvoiceExcelFileService
    )
    {
        $this->indexInvoiceService = $indexInvoiceService;
        $this->generateInvoiceExcelFileService = $generateInvoiceExcelFileService;
    }

    public function __invoke(array $data, array $paginationParam): Collection|string
    {
        $invoices = ($this->indexInvoiceService)($data, $paginationParam);

        if (isset($data['export']) && $data['export']) {
            $excelFile = ($this->generateInvoiceExcelFileService)($invoices);
            // upload to object store and get its link
            return 'link-to-file';
        } else {
            return $invoices;
        }
    }
}

<?php

namespace App\Services\Admin\Invoice;

use App\Repositories\Base\BaseRepository;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use App\Services\Invoice\Item\ListItemByCriteriaService;
use Illuminate\Database\Query\Builder;

class IndexInvoiceService
{
    private InvoiceRepositoryInterface $invoiceRepository;
    private ListItemByCriteriaService $listItemByCriteriaService;

    public function __construct(
        InvoiceRepositoryInterface $invoiceRepository,
        ListItemByCriteriaService  $listItemByCriteriaService,
    )
    {
        $this->invoiceRepository = $invoiceRepository;
        $this->listItemByCriteriaService = $listItemByCriteriaService;
    }

    public function __invoke(array $data, array $paginationParam)
    {
        $query = $this->invoiceRepository->newQuery();

        // Check if "search" "from_date" "to_date" query strings are set
        // and if yes fetch related Invoice Items to them to filter down the Invoice query
        $invoiceItemCriteria = [];
        if (!empty($data['search'])) {
            $invoiceItemCriteria['keyword'] = $data['search'];
        }
        if (!empty($data['from_date'])) {
            $invoiceItemCriteria['from_date'] = $data['from_date'];
        }
        if (!empty($data['to_date'])) {
            $invoiceItemCriteria['to_date'] = $data['to_date'];
        }
        if (!empty($invoiceItemCriteria)) {
            $items = ($this->listItemByCriteriaService)($invoiceItemCriteria);
            $query->whereIn('id', $items->pluck('invoice_id')->toArray());
        }

        if (!empty($data['client_id'])) {
            $query->where('client_id', '=', $data['client_id']);
        }
        if (!empty($data['invoice_id'])) {
            $query->where('id', '=', $data['invoice_id']);
        }
        if (!empty($data['payment_method'])) {
            $query->where('payment_method', '=', $data['payment_method']);
        }
        if (!empty($data['status'])) {
            $query->where('status', '=', $data['status']);
        }
        if (!empty($data['invoice_date'])) {
            $query->whereDate('created_at', '=', $data['invoice_date']);
        }
        if (!empty($data['paid_date'])) {
            $query->whereDate('paid_at', '=', $data['paid_date']);
        }
        if (!empty($data['due_date'])) {
            $query->whereDate('due_date', '=', $data['due_date']);
        }
        if (!empty($data['invoice_number'])) {
            $query->whereHas('invoiceNumber', function (Builder $query) use ($data) {
                $query->where('invoice_number', $data['invoice_number']);
            });
        }

        $query->orderBy(
            $data['sort'] ?? BaseRepository::DEFAULT_SORT_COLUMN,
            $data['sortDirection'] ?? BaseRepository::DEFAULT_SORT_COLUMN_DIRECTION,
        );

        if (isset($data['export']) && $data['export']) {
            return $query->get();
        }

        return $query->paginate($paginationParam['perPage']);
    }
}

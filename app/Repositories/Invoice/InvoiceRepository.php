<?php

namespace App\Repositories\Invoice;

use App\Models\Invoice;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class InvoiceRepository extends BaseRepository implements InvoiceRepositoryInterface
{
    public string $model = Invoice::class;

    /**
     * @throws BindingResolutionException
     */
    public function adminIndex(array $data, array $paginationParam)
    {
        $query = self::newQuery();

        if (!empty($data['search'])) {
            $query->where(function (Builder $query) use ($data) {
                $query->where('id', "LIKE", '%' . $data['search'] . '%')
                    ->orWhere("total", $data['search'] . '.00');

                if (!empty($data['item_invoice_ids'])) {
                    $query->orWhereIn('id', $data['item_invoice_ids']);
                }
            });
        }
        if (!empty($data['from_date'])) {
            $query->whereDate('created_at', '>=', $data['from_date']);
        }
        if (!empty($data['to_date'])) {
            $query->whereDate('created_at', '<=', $data['to_date']);
        }
        if (!empty($data['non_checked'])) {
            $query->whereNull('admin_id')
                ->whereIn('status', [Invoice::STATUS_PAID, Invoice::STATUS_REFUNDED]);
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

        return $this->paginate($query);
    }
}

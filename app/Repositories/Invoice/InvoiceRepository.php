<?php

namespace App\Repositories\Invoice;

use App\Models\Invoice;
use App\Models\Item;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class InvoiceRepository extends BaseRepository implements InvoiceRepositoryInterface
{
    public string $model = Invoice::class;

    public function adminIndex(array $data): Collection|LengthAwarePaginator
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

    public function profileIndex(array $data): LengthAwarePaginator
    {
        $query = self::newQuery();
        $query->where('client_id', $data['client_id']);
        if (!empty($data['search'])) {
            $query->where(function (Builder $query) use ($data) {
                $query->where('id', "LIKE", '%' . $data['search'] . '%')
                    ->orWhere("total", $data['search'] . '.00');

                if (!empty($data['item_invoice_ids'])) {
                    $query->orWhereIn('id', $data['item_invoice_ids']);
                }
            });
        }
        if (!empty($data['status'])) {
            if ($data['status'] == Invoice::STATUS_UNPAID) {
                $query->whereIn('status', [
                    Invoice::STATUS_UNPAID,
                    Invoice::STATUS_COLLECTIONS,
                    Invoice::STATUS_PAYMENT_PENDING,
                ]);
            } else {
                $query->where('status', '=', $data['status']);
            }
        }
        $query->where('status', '<>', Invoice::STATUS_DRAFT);
        $query->where('is_mass_payment', false);

        $query->orderBy(
            $data['sort'] ?? BaseRepository::DEFAULT_SORT_COLUMN,
            $data['sortDirection'] ?? BaseRepository::DEFAULT_SORT_COLUMN_DIRECTION,
        );

        return self::paginate($query);
    }

    public function profileListEverything(int $clientId): Collection
    {
        return self::newQuery()
            ->where('client_id', $clientId)
            ->where('status', Invoice::STATUS_PAID)
            ->where('is_credit', false)
            ->where('is_mass_payment', false)
            ->where(function (Builder $builder) {
                $builder->where('total', '<=', -50000)
                    ->orWhere('total', '>=', 50000);
            })
            ->get(['id', 'paid_at', 'total']);
    }

    public function prepareInvoicesForMassPayment(array $data): Collection
    {
        return self::newQuery()
            ->where('client_id', $data['client_id'])
            ->where('status', Invoice::STATUS_UNPAID)
            ->where('is_credit', false)
            ->where('is_mass_payment', false)
            ->whereIn('id', $data['invoice_ids'])
            ->get();
    }

    public function internalIndex(array $data): Collection
    {
        return self::newQuery()
            ->when(!empty($data['client_id']), function (Builder $builder) use ($data) {
                $builder->where('client_id', $data['client_id']);
            })
            ->when(!empty($data['status']), function (Builder $builder) use ($data) {
                $builder->where('status', $data['status']);
            })
            ->whereHas('items', function (Builder $builder) use ($data) {
                $builder->where('invoiceable_id', $data['invoiceable_id']);
                $builder->where('invoiceable_type', $data['invoiceable_type']);
            })
            ->get();
    }

    public function internalIndexMyInvoice(array $data): LengthAwarePaginator
    {
        $query = self::newQuery()
            ->where(function (Builder $builder) use ($data) {
                $builder->whereHas('items', function (Builder $builder) use ($data) {
                    $builder->whereIn('invoiceable_id', $data['invoiceable_ids']);
                    $builder->where('invoiceable_type', Item::TYPE_CLOUD);
                });
            })
            ->orWhere(function (Builder $builder) use ($data) {
                $builder->whereHas('items', function (Builder $builder) use ($data) {
                    $builder->whereIn('invoiceable_id', $data['invoiceable_ids']);
                    $builder->where('invoiceable_type', Item::TYPE_ADD_CLOUD_CREDIT);
                });
            })
            ->orWhere(function (Builder $builder) use ($data) {
                $builder->where('is_credit', true);
                $builder->where('client_id', $data['client_id']);
            });
        if (!empty($data['search'])) {
            $query->where('id', $data['search']);
        }
        if (!empty($data['status'])) {
            $query->where('status', $data['status']);
        }

        $query->orderBy(
            $data['sort'] ?? BaseRepository::DEFAULT_SORT_COLUMN,
            $data['sortDirection'] ?? BaseRepository::DEFAULT_SORT_COLUMN_DIRECTION,
        );

        return self::paginate($query);
    }

    public function count(): int
    {
        return self::newQuery()
            ->count();
    }

    public function countToday(): int
    {
        return self::newQuery()
            ->whereDate('created_at', now())
            ->count();
    }

    public function countPaid(): int
    {
        return self::newQuery()
            ->where('status', Invoice::STATUS_PAID)
            ->count();
    }

    public function incomeToday(): float
    {
        return self::newQuery()
            ->whereDate('paid_at', now())
            ->where('is_credit', false)
            ->where('is_mass_payment', false)
            ->sum('total');
    }

    public function reportLatest(): Collection
    {
        return self::newQuery()
            ->where('status', Invoice::STATUS_PAID)
            ->limit(15)
            ->orderByDesc('id')
            ->get();
    }
}

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

    public function reportRevenue(): array
    {
        $dates = finance_report_dates();

        $totalRevenue = self::newQuery()
            ->whereIn('status', [Invoice::STATUS_PAID, Invoice::STATUS_COLLECTIONS])
            ->where('is_credit', false)
            ->where('is_mass_payment', false)
            ->sum('total');
        $currentMonthRevenue = self::newQuery()
            ->where(function (Builder $query) use ($dates) {
                $query->where('status', Invoice::STATUS_PAID)
                    ->whereDate('paid_at', '>=', $dates['start_of_current_month'])
                    ->whereDate('paid_at', '<=', now());
            })
            ->orWhere(function (Builder $query) use ($dates) {
                $query->where('status', Invoice::STATUS_COLLECTIONS)
                    ->whereDate('created_at', '>=', $dates['start_of_current_month'])
                    ->whereDate('created_at', '<=', now());
            })
            ->where('is_credit', false)
            ->where('is_mass_payment', false)
            ->sum('total');
        $currentYearRevenue = self::newQuery()
            ->where(function (Builder $query) use ($dates) {
                $query->where('status', Invoice::STATUS_PAID)
                    ->whereDate('paid_at', '>=', $dates['start_of_current_year'])
                    ->whereDate('paid_at', '<=', now());
            })
            ->orWhere(function (Builder $query) use ($dates) {
                $query->where('status', Invoice::STATUS_COLLECTIONS)
                    ->whereDate('created_at', '>=', $dates['start_of_current_year'])
                    ->whereDate('created_at', '<=', now());
            })
            ->where('is_credit', false)
            ->where('is_mass_payment', false)
            ->sum('total');
        $lastMonthRevenue = self::newQuery()
            ->where(function (Builder $query) use ($dates) {
                $query->where('status', Invoice::STATUS_PAID)
                    ->whereDate('paid_at', '>=', $dates['last_month']['from'])
                    ->whereDate('paid_at', '<=', $dates['last_month']['to']);
            })
            ->orWhere(function (Builder $query) use ($dates) {
                $query->where('status', Invoice::STATUS_COLLECTIONS)
                    ->whereDate('created_at', '>=', $dates['last_month']['from'])
                    ->whereDate('created_at', '<=', $dates['last_month']['to']);
            })
            ->where('is_credit', false)
            ->where('is_mass_payment', false)
            ->sum('total');

        return [
            'total_revenue' => $totalRevenue,
            'current_month_revenue' => $currentMonthRevenue,
            'last_year_revenue' => $lastMonthRevenue,
            'current_year_revenue' => $currentYearRevenue,
        ];
    }

    public function reportTax(): array
    {
        $dates = finance_report_dates();

        $totalTax = self::newQuery()
            ->whereIn('status', [Invoice::STATUS_PAID, Invoice::STATUS_COLLECTIONS])
            ->where('is_credit', false)
            ->where('is_mass_payment', false)
            ->sum('tax');
        $currentMonthTax = self::newQuery()
            ->where(function (Builder $query) use ($dates) {
                $query->where('status', Invoice::STATUS_PAID)
                    ->whereDate('paid_at', '>=', $dates['start_of_current_month'])
                    ->whereDate('paid_at', '<=', now());
            })
            ->orWhere(function (Builder $query) use ($dates) {
                $query->where('status', Invoice::STATUS_COLLECTIONS)
                    ->whereDate('created_at', '>=', $dates['start_of_current_month'])
                    ->whereDate('created_at', '<=', now());
            })
            ->where('is_credit', false)
            ->where('is_mass_payment', false)
            ->sum('tax');
        $currentYearTax = self::newQuery()
            ->where(function (Builder $query) use ($dates) {
                $query->where('status', Invoice::STATUS_PAID)
                    ->whereDate('paid_at', '>=', $dates['start_of_current_year'])
                    ->whereDate('paid_at', '<=', now());
            })
            ->orWhere(function (Builder $query) use ($dates) {
                $query->where('status', Invoice::STATUS_COLLECTIONS)
                    ->whereDate('created_at', '>=', $dates['start_of_current_year'])
                    ->whereDate('created_at', '<=', now());
            })
            ->where('is_credit', false)
            ->where('is_mass_payment', false)
            ->sum('tax');
        $lastMonthTax = self::newQuery()
            ->where(function (Builder $query) use ($dates) {
                $query->where('status', Invoice::STATUS_PAID)
                    ->whereDate('paid_at', '>=', $dates['last_month']['from'])
                    ->whereDate('paid_at', '<=', $dates['last_month']['to']);
            })
            ->orWhere(function (Builder $query) use ($dates) {
                $query->where('status', Invoice::STATUS_COLLECTIONS)
                    ->whereDate('created_at', '>=', $dates['last_month']['from'])
                    ->whereDate('created_at', '<=', $dates['last_month']['to']);
            })
            ->where('is_credit', false)
            ->where('is_mass_payment', false)
            ->sum('tax');

        return [
            'total_tax' => $totalTax,
            'current_month_tax' => $currentMonthTax,
            'last_year_tax' => $lastMonthTax,
            'current_year_tax' => $currentYearTax,
        ];
    }

    public function reportCollection(): array
    {
        $dates = finance_report_dates();

        $totalCollection = self::newQuery()
            ->where('status', Invoice::STATUS_COLLECTIONS)
            ->where('is_credit', false)
            ->where('is_mass_payment', false)
            ->sum('tax');
        $currentMonthCollection = self::newQuery()
            ->where('status', Invoice::STATUS_COLLECTIONS)
            ->whereDate('created_at', '>=', $dates['start_of_current_month'])
            ->whereDate('created_at', '<=', now())
            ->where('is_mass_payment', false)
            ->sum('tax');
        $currentYearCollection = self::newQuery()
            ->where('status', Invoice::STATUS_COLLECTIONS)
            ->whereDate('created_at', '>=', $dates['start_of_current_year'])
            ->whereDate('created_at', '<=', now())
            ->where('is_mass_payment', false)
            ->sum('tax');
        $lastMonthCollection = self::newQuery()
            ->where('status', Invoice::STATUS_COLLECTIONS)
            ->whereDate('created_at', '>=', $dates['last_month']['from'])
            ->whereDate('created_at', '<=', $dates['last_month']['to'])
            ->where('is_mass_payment', false)
            ->sum('tax');

        return [
            'total_collection' => $totalCollection,
            'current_month_collection' => $currentMonthCollection,
            'last_year_collection' => $lastMonthCollection,
            'current_year_collection' => $currentYearCollection,
        ];
    }

    public function rahkaranQuery($from = null, $to = null): Builder
    {
        $dates = finance_report_dates();
        if (is_null($from)) {
            $from = $dates['start_of_current_month'];
        }
        if (is_null($to)) {
            $to = now();
        }
        $query = self::newQuery()
            ->where(function (Builder $query) use ($from, $to) {
                $query->orWhere(function (Builder $status_query) use ($from, $to) {
                    $status_query->whereDate('created_at', '>=', $from);
                    $status_query->whereDate('created_at', '<=', $to);
                    $status_query->where('status', Invoice::STATUS_COLLECTIONS);
                });
                $query->orWhere(function (Builder $status_query) use ($from, $to) {
                    $status_query->whereDate('paid_at', '>=', $from);
                    $status_query->whereDate('paid_at', '<=', $to);
                });
            });

        // Only paid or refunded invoices can be imported
        $query->whereIn('status', [
            Invoice::STATUS_PAID,
            Invoice::STATUS_COLLECTIONS,
            Invoice::STATUS_REFUNDED,
        ]);

        $query->where('is_mass_payment', 0);
        $query->where('is_credit', 0);

        $query->where('tax', '>', 0);

        // Filters out imported invoices
        $query->whereNull('rahkaran_id');

        return $query;
    }
}

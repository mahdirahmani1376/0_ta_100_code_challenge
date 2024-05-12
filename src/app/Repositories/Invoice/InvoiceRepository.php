<?php

namespace App\Repositories\Invoice;

use App\Models\BankGateway;
use App\Models\Invoice;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class InvoiceRepository extends BaseRepository implements InvoiceRepositoryInterface
{
    public string $model = Invoice::class;

    public function index(array $data): Collection|LengthAwarePaginator
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
        $dateField = 'created_at';
        if (!empty($data['date_field'])) {
            $dateField = $data['date_field'];
        }
        if (!empty($data['from_date'])) {
            $query->whereDate($dateField, '>=', $data['from_date']);
        }
        if (!empty($data['to_date'])) {
            $query->whereDate($dateField, '<=', $data['to_date']);
        }
        if (!empty($data['non_checked'])) {
            $query->whereNull('admin_id')
                ->whereIn('status', [Invoice::STATUS_PAID, Invoice::STATUS_REFUNDED]);
        }
        if (!empty($data['profile_id'])) {
            $query->where('profile_id', '=', $data['profile_id']);
        }
        if (!empty($data['invoice_id'])) {
            $query->where('id', '=', $data['invoice_id']);
        }
        if (!empty($data['invoice_ids'])) {
            $query->whereIn('id', $data['invoice_ids']);
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
        if (isset($data['is_credit'])) {
            $query->where('is_credit', $data['is_credit']);
        }
        if (isset($data['is_mass_payment'])) {
            $query->where('is_mass_payment', $data['is_mass_payment']);
        }
        if (!empty($data['invoice_number'])) {
            $query->whereHas('invoiceNumber', function (Builder $query) use ($data) {
                $query->where('invoice_number', $data['invoice_number']);
            });
        }
        if (!empty($data['invoiceable_id']) || !empty($data['invoiceable_type'])) {
            $query->whereHas('items', function (Builder $query) use ($data) {
                $query->when(
                    !empty($data['invoiceable_id']),
                    fn(Builder $query) => $query->where('invoiceable_id', $data['invoiceable_id'])
                );
                $query->when(
                    !empty($data['invoiceable_type']),
                    fn(Builder $query) => $query->where('invoiceable_type', $data['invoiceable_type'])
                );
            });
        }
        /**
         * items => [
         *      ['invoiceable_ids' => array, 'invoiceable_type' => string],
         *      ['invoiceable_ids' => array, 'invoiceable_type' => string],
         *      ...
         *   ]
         */
        if (!empty($data['items'])) {
            $query->where(function (Builder $query) use ($data) {
                foreach ($data['items'] as $item) {
                    $query->orWhereHas('items', function (Builder $query) use ($item) {
                        $query->when(
                            !empty($item['invoiceable_ids']),
                            fn(Builder $query) => $query->whereIn('invoiceable_id', $item['invoiceable_ids'])
                        );
                        $query->when(
                            !empty($item['invoiceable_type']),
                            fn(Builder $query) => $query->where('invoiceable_type', $item['invoiceable_type'])
                        );
                    });
                }
            });
        }

        if (isset($data['export']) && $data['export']) {
            $query->when(!empty($data['per_page']), fn(Builder $query) => $query->limit($data['per_page']));

            return self::sortQuery($query)->get();
        }

        return self::paginate($query);
    }

    public function indexEverything(int $profileId): Collection
    {
        return self::newQuery()
            ->where('profile_id', $profileId)
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
            ->where('profile_id', $data['profile_id'])
            ->where('status', Invoice::STATUS_UNPAID)
            ->where('is_credit', false)
            ->where('is_mass_payment', false)
            ->whereIn('id', $data['invoice_ids'])
            ->get();
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

    public function reportRevenue($from, $to): array
    {
        [$from, $to] = finance_report_dates($from, $to);

        $revenue = self::newQuery()
            ->where(function (Builder $query) use ($to, $from) {
                $query->where(function (Builder $query) use ($from, $to) {
                    $query->where('status', Invoice::STATUS_PAID)
                        ->whereDate('paid_at', '>=', $from)
                        ->whereDate('paid_at', '<=', $to);
                });
                $query->orWhere(function (Builder $query) use ($from, $to) {
                    $query->where('status', Invoice::STATUS_COLLECTIONS)
                        ->whereDate('created_at', '>=', $from)
                        ->whereDate('created_at', '<=', $to);
                });
            })
            ->where('is_credit', false)
            ->where('is_mass_payment', false);

        return [
            'count' => $revenue->count(),
            'sum_total' => $revenue->sum('total'),
            'sum_tax' => $revenue->sum('tax'),
        ];
    }

    public function reportRevenueBasedOnGateway($from, $to): array
    {
        [$from, $to] = finance_report_dates($from, $to);

        $credit = self::newQuery()
            ->where(function (Builder $query) use ($to, $from) {
                $query->where(function (Builder $query) use ($from, $to) {
                    $query->where('status', Invoice::STATUS_PAID)
                        ->whereDate('paid_at', '>=', $from)
                        ->whereDate('paid_at', '<=', $to);
                });
                $query->orWhere(function (Builder $query) use ($from, $to) {
                    $query->where('status', Invoice::STATUS_COLLECTIONS)
                        ->whereDate('created_at', '>=', $from)
                        ->whereDate('created_at', '<=', $to);
                });
            })
            ->where('payment_method', Invoice::PAYMENT_METHOD_CREDIT)
            ->where('is_mass_payment', false);
        $onlineGateway = [];
        foreach (BankGateway::cursor() as $bankGateway) {
            $query = self::newQuery()
                ->where(function (Builder $query) use ($to, $from) {
                    $query->where(function (Builder $query) use ($from, $to) {
                        $query->where('status', Invoice::STATUS_PAID)
                            ->whereDate('paid_at', '>=', $from)
                            ->whereDate('paid_at', '<=', $to);
                    });
                    $query->orWhere(function (Builder $query) use ($from, $to) {
                        $query->where('status', Invoice::STATUS_COLLECTIONS)
                            ->whereDate('created_at', '>=', $from)
                            ->whereDate('created_at', '<=', $to);
                    });
                })
                ->where('payment_method', $bankGateway->name)
                ->where('is_mass_payment', false);
            $onlineGateway[$bankGateway->name] = [
                'sum' => $query->sum('total'),
                'count' => $query->count(),
            ];
        }

        return [
            'online' => $onlineGateway,
            'credit_sum' => $credit->sum('total'),
            'credit_count' => $credit->count(),
        ];
    }

    public function reportCollection($from, $to): array
    {
        [$from, $to] = finance_report_dates($from, $to);

        $collection = self::newQuery()
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->where('status', Invoice::STATUS_COLLECTIONS)
            ->where('is_credit', false)
            ->where('is_mass_payment', false);

        return [
            'count_collection' => $collection->count(),
            'sum_collection' => $collection->sum('total'),
        ];
    }

    public function rahkaranQuery($from = null, $to = null): Builder
    {
        [$from, $to] = finance_report_dates($from, $to);

        $query = self::newQuery()
            ->where(function (Builder $query) use ($to, $from) {
                $query->where(function (Builder $query) use ($from, $to) {
                    $query->where('status', Invoice::STATUS_PAID)
                        ->whereDate('paid_at', '>=', $from)
                        ->whereDate('paid_at', '<=', $to);
                });
                $query->orWhere(function (Builder $query) use ($from, $to) {
                    $query->whereIn('status', [Invoice::STATUS_COLLECTIONS, Invoice::STATUS_REFUNDED])
                        ->whereDate('created_at', '>=', $from)
                        ->whereDate('created_at', '<=', $to);
                });
            });

        $query->where('is_mass_payment', false);
        $query->where('is_credit', false);

        $query->where('tax', '>', 0);

        // Filters out imported invoices
        $query->whereNull('rahkaran_id');

        return $query;
    }

    public function hourlyReport($from, $to): float
    {
        return self::newQuery()
            ->where('status', Invoice::STATUS_PAID)
            ->where('is_credit', false)
            ->where('is_mass_payment', false)
            ->where('paid_at', '>=', $from)
            ->where('paid_at', '<=', $to)
            ->sum('total');
    }
}

<?php

namespace App\Repositories\Transaction;

use App\Models\BankGateway;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Transaction\Interface\TransactionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class TransactionRepository extends BaseRepository implements TransactionRepositoryInterface
{
    public string $model = Transaction::class;

    public function refundSuccessfulTransactions(Invoice $invoice)
    {
        return self::newQuery()
            ->where('invoice_id', $invoice->getKey())
            ->where('status', Transaction::STATUS_SUCCESS)
            ->update(['status' => Transaction::STATUS_REFUND]);
    }

    public function sumOfPaidTransactions(Invoice $invoice): int
    {
        return $this->newQuery()
            ->where('invoice_id', $invoice->getKey())
            ->where('status', Transaction::STATUS_SUCCESS)
            ->sum('amount');
    }

    public function getLastSuccessfulTransaction(Invoice $invoice)
    {
        return $this->newQuery()
            ->where('invoice_id', $invoice->getKey())
            ->where('amount', '>', 10)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function findByTrackingCode($trackingCode): ?Transaction
    {
        return self::newQuery()
            ->where('tracking_code', $trackingCode)
            ->first();
    }

    public function adminIndex(array $data): Collection|LengthAwarePaginator
    {
        $query = self::newQuery();

        if (!empty($data['search'])) {
            $query->where(function (Builder $query) use ($data) {
                return $query->where('reference_id', 'LIKE', '%' . $data['search'] . '%')
                    ->orWhere('id', '%' . $data['search'] . '%')
                    ->orWhere('invoice_id', 'LIKE', '%' . $data['search'] . '%')
                    ->orWhere('tracking_code', 'LIKE', '%' . $data['search'] . '%')
                    ->orWhere("description", "LIKE", '%' . $data['search'] . '%');
            });
        }
        if (!empty($data['date'])) {
            $query->whereDate('created_at', '=', $data['date']);
        }
        if (!empty($data['status'])) {
            $query->where('status', '=', $data['status']);
        }
        if (!empty($data['tracking_code'])) {
            $query->where('tracking_code', '=', $data['tracking_code']);
        }
        if (!empty($data['reference_id'])) {
            $query->where('reference_id', '=', $data['reference_id']);
        }
        if (!empty($data['payment_method'])) {
            $query->where('payment_method', '=', $data['payment_method']);
        }
        if (!empty($data['invoice_id'])) {
            $query->where('invoice_id', '=', $data['invoice_id']);
        }
        if (!empty($data['client_id'])) {
            $query->where('client_id', '=', $data['client_id']);
        }
        if (!empty($data['to_date'])) {
            $query->whereDate('created_at', '<=', $data['to_date']);
        }
        if (!empty($data['from_date'])) {
            $query->whereDate('created_at', '>=', $data['from_date']);
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

    public function profileIndex(array $data): Collection|LengthAwarePaginator
    {
        $query = self::newQuery();

        $query->where('client_id', $data['client_id']);
        if (!empty($data['search'])) {
            $query->where(function (Builder $query) use ($data) {
                return $query->where('reference_id', 'LIKE', '%' . $data['search'] . '%')
                    ->orWhere('id', '%' . $data['search'] . '%')
                    ->orWhere('invoice_id', 'LIKE', '%' . $data['search'] . '%')
                    ->orWhere('tracking_code', 'LIKE', '%' . $data['search'] . '%')
                    ->orWhere("description", "LIKE", '%' . $data['search'] . '%');
            });
        }
        if (!empty($data['status'])) {
            $query->where('status', '=', $data['status']);
        }
        if (!empty($data['payment_method'])) {
            $query->where('payment_method', '=', $data['payment_method']);
        }
        $query->orderBy(
            $data['sort'] ?? BaseRepository::DEFAULT_SORT_COLUMN,
            $data['sortDirection'] ?? BaseRepository::DEFAULT_SORT_COLUMN_DIRECTION,
        );

        return $this->paginate($query);
    }

    public function profileListEverything(int $clientId): Collection
    {
        return self::newQuery()
            ->where('client_id', $clientId)
            ->where('status', Transaction::STATUS_SUCCESS)
            ->whereNotIn('payment_method', [
                Transaction::PAYMENT_METHOD_WALLET_BALANCE,
                Transaction::PAYMENT_METHOD_CREDIT,
            ])
            ->where('amount', '>=', 50000)
            ->get(['id', 'updated_at', 'invoice_id', 'amount', 'description',]);
    }

    public function count(): int
    {
        return self::newQuery()->count();
    }

    public function successCount(): int
    {
        return self::newQuery()
            ->whereDate('created_at', now())
            ->where('status', Transaction::STATUS_SUCCESS)
            ->count();
    }

    public function failCount(): int
    {
        return self::newQuery()
            ->whereDate('created_at', now())
            ->where('status', Transaction::STATUS_FAIL)
            ->count();
    }

    public function reportLatest(): Collection
    {
        return self::newQuery()
            ->limit(15)
            ->orderByDesc('id')
            ->get();
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
        // TODO check this query
        $query = self::newQuery()
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->where(function (Builder $query) {
                $query->whereHas('invoice', function (Builder $query) {
                    $query->whereIn('status', [Invoice::STATUS_PAID, Invoice::STATUS_COLLECTIONS]);
                    $query->where('is_credit', false);
                });
                $query->where('status', Transaction::STATUS_SUCCESS);
            });
        $query->orWhere(function (Builder $query) {
            $query->whereHas('invoice', function (Builder $query) {
                $query->whereIn('status', [Invoice::STATUS_PAID, Invoice::STATUS_CANCELED]);
                $query->where('is_credit', false);
            });
            $query->where('status', Transaction::STATUS_REFUND);
            $query->where('payment_method', '<>', Transaction::PAYMENT_METHOD_CREDIT);
        });

        // Filters out imported transactions
        $query->whereNull('rahkaran_id');

        // Filter Barter Transactions
        $query->where('payment_method', '<>', Transaction::PAYMENT_METHOD_BARTER);

        return $query;
    }

    public function reportRevenueBasedOnGateway(): array
    {
        $dates = finance_report_dates();

        $offlineTotal = self::newQuery()
            ->where('payment_method', Transaction::PAYMENT_METHOD_OFFLINE)
            ->where('status', Transaction::STATUS_SUCCESS)
            ->sum('amount');
        $offlineCurrentMonth = self::newQuery()
            ->whereDate('created_at', '>=', $dates['start_of_current_month'])
            ->whereDate('created_at', '<=', now())
            ->where('payment_method', Transaction::PAYMENT_METHOD_OFFLINE)
            ->where('status', Transaction::STATUS_SUCCESS)
            ->sum('amount');
        $offlineCurrentYear = self::newQuery()
            ->whereDate('created_at', '>=', $dates['start_of_current_year'])
            ->whereDate('created_at', '<=', now())
            ->where('payment_method', Transaction::PAYMENT_METHOD_OFFLINE)
            ->where('status', Transaction::STATUS_SUCCESS)
            ->sum('amount');
        $walletTotal = self::newQuery()
            ->where('payment_method', Transaction::PAYMENT_METHOD_WALLET_BALANCE)
            ->where('status', Transaction::STATUS_SUCCESS)
            ->sum('amount');
        $walletCurrentMonth = self::newQuery()
            ->whereDate('created_at', '>=', $dates['start_of_current_month'])
            ->whereDate('created_at', '<=', now())
            ->where('payment_method', Transaction::PAYMENT_METHOD_WALLET_BALANCE)
            ->where('status', Transaction::STATUS_SUCCESS)
            ->sum('amount');
        $walletCurrentYear = self::newQuery()
            ->whereDate('created_at', '>=', $dates['start_of_current_year'])
            ->whereDate('created_at', '<=', now())
            ->where('payment_method', Transaction::PAYMENT_METHOD_WALLET_BALANCE)
            ->where('status', Transaction::STATUS_SUCCESS)
            ->sum('amount');
        $onlineTransactionsBasedOnGateway = [];
        foreach (BankGateway::cursor() as $bankGateway) {
            $total = self::newQuery()
                ->where('payment_method', $bankGateway->name)
                ->where('status', Transaction::STATUS_SUCCESS)
                ->sum('amount');
            $currentMonth = self::newQuery()
                ->whereDate('created_at', '>=', $dates['start_of_current_month'])
                ->whereDate('created_at', '<=', now())
                ->where('payment_method', $bankGateway->name)
                ->where('status', Transaction::STATUS_SUCCESS)
                ->sum('amount');
            $currentYear = self::newQuery()
                ->whereDate('created_at', '>=', $dates['start_of_current_year'])
                ->whereDate('created_at', '<=', now())
                ->where('payment_method', $bankGateway->name)
                ->where('status', Transaction::STATUS_SUCCESS)
                ->sum('amount');
            $onlineTransactionsBasedOnGateway[$bankGateway->name] = [
                'total' => $total,
                'current_month' => $currentMonth,
                'current_year' => $currentYear
            ];
        }

        return [
            'offline' => [
                'total' => $offlineTotal,
                'current_month' => $offlineCurrentMonth,
                'current_year' => $offlineCurrentYear,
            ],
            'online' => $onlineTransactionsBasedOnGateway,
            'wallet' => [
                'total' => $walletTotal,
                'current_month' => $walletCurrentMonth,
                'current_year' => $walletCurrentYear,
            ],
        ];
    }
}

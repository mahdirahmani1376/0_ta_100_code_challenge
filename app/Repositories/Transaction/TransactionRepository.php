<?php

namespace App\Repositories\Transaction;

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
}

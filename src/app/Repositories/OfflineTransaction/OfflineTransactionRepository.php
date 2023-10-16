<?php

namespace App\Repositories\OfflineTransaction;

use App\Models\Invoice;
use App\Models\OfflineTransaction;
use App\Models\Transaction;
use App\Repositories\Base\BaseRepository;
use App\Repositories\OfflineTransaction\Interface\OfflineTransactionRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class OfflineTransactionRepository extends BaseRepository implements OfflineTransactionRepositoryInterface
{
    public string $model = OfflineTransaction::class;

    /**
     * @throws BindingResolutionException
     */
    public function adminIndex(array $data): LengthAwarePaginator
    {
        $query = self::newQuery();

        if (!empty($data['search'])) {
            $query->where(function (Builder $query) use ($data) {
                $query->where('tracking_code', "LIKE", '%' . $data['search'] . '%')
                    ->orWhere('invoice_id', "LIKE", '%' . $data['search'] . '%')
                    ->orWhere('client_id', "LIKE", '%' . $data['search'] . '%');
            });
        }
        if (!empty($data['from_date'])) {
            $query->whereDate('created_at', '>=', $data['from_date']);
        }
        if (!empty($data['to_date'])) {
            $query->whereDate('created_at', '<=', $data['to_date']);
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

    /**
     * @throws BindingResolutionException
     */
    public function adminIndexSimilar(OfflineTransaction $offlineTransaction): LengthAwarePaginator
    {
        $query = self::newQuery()
            ->where('id', '<>', $offlineTransaction->getKey())
            ->where('amount', $offlineTransaction->amount)
            ->whereDate('paid_at', $offlineTransaction->paid_at)
            ->orderBy(
                $data['sort'] ?? BaseRepository::DEFAULT_SORT_COLUMN,
                $data['sortDirection'] ?? BaseRepository::DEFAULT_SORT_COLUMN_DIRECTION,
            );

        return self::paginate($query);
    }

    public function sumOfVerifiedOfflineTransactions(Invoice $invoice): Collection
    {
        return self::newQuery()
            ->where('invoice_id', $invoice->getKey())
            ->where('status', OfflineTransaction::STATUS_CONFIRMED)
            ->sum('amount');
    }

    public function findByTransaction(Transaction $transaction): ?OfflineTransaction
    {
        return self::newQuery()
            ->where('tracking_code', $transaction->tracking_code)
            ->where('invoice_id', $transaction->invoice_id)
            ->first();
    }

    public function countToday(): int
    {
        return self::newQuery()
            ->whereDate('paid_at', now())
            ->count();
    }

    public function countRejected(): int
    {
        return self::newQuery()
            ->where('status', OfflineTransaction::STATUS_REJECTED)
            ->count();
    }

    public function reportLatest(): Collection
    {
        return self::newQuery()
            ->where('status', OfflineTransaction::STATUS_PENDING)
            ->limit(15)
            ->orderByDesc('id')
            ->get();
    }
}

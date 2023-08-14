<?php

namespace App\Repositories\OfflineTransaction;

use App\Models\OfflineTransaction;
use App\Repositories\Base\BaseRepository;
use App\Repositories\OfflineTransaction\Interface\OfflineTransactionRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

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
}

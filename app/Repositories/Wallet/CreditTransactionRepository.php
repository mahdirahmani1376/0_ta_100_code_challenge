<?php

namespace App\Repositories\Wallet;

use App\Models\CreditTransaction;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Wallet\Interface\CreditTransactionRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CreditTransactionRepository extends BaseRepository implements CreditTransactionRepositoryInterface
{
    public string $model = CreditTransaction::class;

    public function indexByClientId(int $clientId): LengthAwarePaginator
    {
        return $this->paginate(
            $this->newQuery()
                ->where('client_id', $clientId)
        );
    }

    public function sum(int $clientId): int
    {
        return $this->newQuery()
            ->where('client_id', $clientId)
            ->sum('amount');
    }

    /**
     * @throws BindingResolutionException
     */
    public function adminIndex(array $data): LengthAwarePaginator
    {
        $query = self::newQuery();

        if (!empty($data['search'])) {
            $query->where(function ($query) use ($data) {
                $query->Where("description", "LIKE", "%" . $data['search'] . "%")
                    ->orWhere('invoice_id', '=', $data['search']);
            });
        }
        if (!empty($data['to_date'])) {
            $query->whereDate('created_at', '<=', $data['to_date']);
        }
        if (!empty($data['from_date'])) {
            $query->whereDate('created_at', '>=', $data['from_date']);
        }
        if (!empty($data['date'])) {
            $query->whereDate('created_at', '=', $data['date']);
        }
        if (!empty($data['client_id'])) {
            $query->where('client_id', '=', $data['client_id']);
        }

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
            ->where(function (Builder $builder) {
                $builder->where('amount', '<=', -50000)
                    ->orWhere('amount', '>=', 50000);
            })
            ->get(['id', 'created_at', 'updated_at', 'invoice_id', 'amount', 'description',]);
    }

    public function internalCloudBulkDelete(array $ids): int
    {
        return self::newQuery()
            ->whereIn('id', $ids)
            ->delete();
    }
}

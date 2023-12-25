<?php

namespace App\Repositories\Wallet;

use App\Models\CreditTransaction;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Wallet\Interface\CreditTransactionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CreditTransactionRepository extends BaseRepository implements CreditTransactionRepositoryInterface
{
    public string $model = CreditTransaction::class;

    public function sum(int $profileId): int
    {
        return $this->newQuery()
            ->where('profile_id', $profileId)
            ->sum('amount');
    }

    public function index(array $data): Collection|LengthAwarePaginator
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
        if (!empty($data['profile_id'])) {
            $query->where('profile_id', '=', $data['profile_id']);
        }

        if (isset($data['export']) && $data['export']) {
            return self::sortQuery($query)->get();
        }

        return self::paginate($query);
    }

    public function indexEverything(int $profileId): Collection
    {
        return self::newQuery()
            ->where('profile_id', $profileId)
            ->where(function (Builder $builder) {
                $builder->where('amount', '<=', -50000)
                    ->orWhere('amount', '>=', 50000);
            })
            ->get(['id', 'created_at', 'updated_at', 'invoice_id', 'amount', 'description',]);
    }

    public function bulkDelete(array $ids): int
    {
        return self::newQuery()
            ->whereIn('id', $ids)
            ->delete();
    }

    public function internalCloudSum(array $ids): float
    {
        return self::newQuery()
            ->whereIn('id', $ids)
            ->sum('amount');
    }

    public function report($from, $to): array
    {
        [$from, $to] = finance_report_dates($from, $to);

        $query = self::newQuery()
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);

        return [
            'count' => $query->count(),
            'sum' => $query->sum('amount'),
        ];
    }
}

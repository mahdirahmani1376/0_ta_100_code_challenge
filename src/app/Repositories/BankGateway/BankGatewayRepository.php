<?php

namespace App\Repositories\BankGateway;

use App\Models\BankGateway;
use App\Repositories\BankGateway\Interface\BankGatewayRepositoryInterface;
use App\Repositories\Base\BaseRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BankGatewayRepository extends BaseRepository implements BankGatewayRepositoryInterface
{
    public string $model = BankGateway::class;

    /**
     * @throws BindingResolutionException
     */
    public function adminIndex(array $data): LengthAwarePaginator
    {
        $query = self::newQuery();
        if (!empty($data['search'])) {
            $query->where(function (Builder $query) use ($data) {
                $query->where('name', 'LIKE', '%' . $data['search'] . '%');
                $query->orWhere('name_fa', 'LIKE', '%' . $data['search'] . '%');
            });
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

    public function all(bool $isAdmin = false): Collection
    {
        return $this->newQuery()
            ->when(!$isAdmin, function (Builder $builder) {
                $builder->where('status', BankGateway::STATUS_ACTIVE);
            })
            ->get();
    }

    public function findByName(string $name): ?BankGateway
    {
        return self::newQuery()
            ->where('name', $name)
            ->where('status', BankGateway::STATUS_ACTIVE)
            ->firstOrFail();
    }
}

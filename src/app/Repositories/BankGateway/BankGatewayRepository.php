<?php

namespace App\Repositories\BankGateway;

use App\Models\BankGateway;
use App\Repositories\BankGateway\Interface\BankGatewayRepositoryInterface;
use App\Repositories\Base\BaseRepository;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * @method BankGateway newQuery()
 */
class BankGatewayRepository extends BaseRepository implements BankGatewayRepositoryInterface
{
    public string $model = BankGateway::class;

    public function index(array $data): Collection|LengthAwarePaginator
    {
        $query = self::newQuery();
        if (!empty($data['profile_id'])) {
            if (!empty($data['direct_payment_providers'])) {
                $query->where(function (Builder $query) use ($data) {
                    $query->where(function (Builder $query) use ($data) {
                        $query->where('is_direct_payment_provider', true);
                        $query->whereIn('name', $data['direct_payment_providers']);
                    });
                    $query->orWhere('is_direct_payment_provider', false);
                });
            } else {
                $query->where('is_direct_payment_provider', false);
            }
        }
        if (!empty($data['search'])) {
            $query->where(function (Builder $query) use ($data) {
                $query->where('name', 'LIKE', '%' . $data['search'] . '%');
                $query->orWhere('name_fa', 'LIKE', '%' . $data['search'] . '%');
            });
        }
        if (!empty($data['status'])) {
            $query->where('status', $data['status']);
        }

        if (isset($data['export']) && $data['export']) {
            return $query->orderBy('order')->get();
        }

        return self::paginate($query);
    }

    public function findByName(string $name): ?BankGateway
    {
        return self::newQuery()
            ->where('name', $name)
            ->where('status', BankGateway::STATUS_ACTIVE)
            ->firstOrFail();
    }
}

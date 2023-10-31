<?php

namespace App\Repositories\BankAccount;

use App\Models\BankAccount;
use App\Repositories\BankAccount\Interface\BankAccountRepositoryInterface;
use App\Repositories\Base\BaseRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class BankAccountRepository extends BaseRepository implements BankAccountRepositoryInterface
{
    public string $model = BankAccount::class;

    /**
     * @throws BindingResolutionException
     */
    public function adminIndex(array $data): LengthAwarePaginator
    {
        $query = self::newQuery();
        if (!empty($data['search'])) {
            $query->where(function (Builder $query) use ($data) {
                $query->where('card_number', 'LIKE', '%' . $data['search'] . '%')
                    ->orWhere('sheba_number', 'LIKE', '%' . $data['search'] . '%')
                    ->orWhere('account_number', 'LIKE', '%' . $data['search'] . '%')
                    ->orWhere('title', 'LIKE', '%' . $data['search'] . '%');
            });
        }
        if (!empty($data['status'])) {
            $query->where('status', $data['status']);
        }

        $query->orderBy(
            $data['sort'] ?? 'display_order',
            $data['sortDirection'] ?? 'asc',
        );

        return self::paginate($query);
    }

    public function publicIndex(array $data): LengthAwarePaginator
    {
        $data['status'] = BankAccount::STATUS_ACTIVE;

        return self::adminIndex($data);
    }
}

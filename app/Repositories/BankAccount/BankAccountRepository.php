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
        $query->orderBy(
            $data['sort'] ?? BaseRepository::DEFAULT_SORT_COLUMN,
            $data['sortDirection'] ?? BaseRepository::DEFAULT_SORT_COLUMN_DIRECTION,
        );

        return self::paginate($query);
    }
}

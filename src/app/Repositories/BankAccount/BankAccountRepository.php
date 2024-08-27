<?php

namespace App\Repositories\BankAccount;

use App\Models\BankAccount;
use App\Repositories\BankAccount\Interface\BankAccountRepositoryInterface;
use App\Repositories\Base\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class BankAccountRepository extends BaseRepository implements BankAccountRepositoryInterface
{
    public string $model = BankAccount::class;

    public function index(array $data): Collection|LengthAwarePaginator
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

	$query->orderBy('order', 'ASC');

        if (isset($data['export']) && $data['export']) {
            return self::sortQuery($query)->get();
        }

        return self::paginate($query);
    }
}

<?php

namespace App\Repositories\ClientCashout;

use App\Models\ClientCashout;
use App\Repositories\Base\BaseRepository;
use App\Repositories\ClientCashout\Interface\ClientCashoutRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ClientCashoutRepository extends BaseRepository implements ClientCashoutRepositoryInterface
{
    public string $model = ClientCashout::class;

    public function index(array $data): LengthAwarePaginator
    {
        $query = self::newQuery();
        if (!empty($data['profile_id'])) {
            $query->where('profile_id', $data['profile_id']);
        }
        if (!empty($data['bank_account_id'])) {
            $query->where('client_bank_account_id', $data['client_bank_account_id']);
        }
        if (!empty($data['status'])) {
            $query->where('status', $data['status']);
        }

        return self::paginate($query);
    }
}

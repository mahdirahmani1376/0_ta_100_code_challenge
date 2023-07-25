<?php

namespace App\Repositories\ClientCashout;

use App\Models\ClientCashout;
use App\Repositories\Base\BaseRepository;
use App\Repositories\ClientCashout\Interface\ClientCashoutRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ClientCashoutRepository extends BaseRepository implements ClientCashoutRepositoryInterface
{
    public string $model = ClientCashout::class;

    public function adminIndex(array $data): LengthAwarePaginator
    {
        $query = self::newQuery();
        if (!empty($data['client_id'])) {
            $query->where('client_id', $data['client_id']);
        }
        if (!empty($data['bank_account_id'])) {
            $query->where('bank_account_id', $data['bank_account_id']);
        }
        if (!empty('status')) {
            $query->where('status', $data['status']);
        }

        return self::paginate($query);
    }
}

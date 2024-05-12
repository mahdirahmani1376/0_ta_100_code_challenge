<?php

namespace App\Repositories\Invoice;

use App\Models\MoadianLog;
use App\Repositories\Base\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class MoadianLogRepository extends BaseRepository implements Interface\MoadianLogRepositoryInterface
{
    public string $model = MoadianLog::class;

    public function index(array $data): Collection|LengthAwarePaginator
    {
        $query = self::newQuery();

        if (!empty($data['search'])) {
            $query->where(function (Builder $query) use ($data) {
                $query->where('invoice_id', 'LIKE', "%" . $data['search'] . "%");
                $query->orWhere('reference_code', 'LIKE', "%" . $data['search'] . "%");
                $query->orWhere('tax_id', 'LIKE', "%" . $data['search'] . "%");
            });
        }
        if (!empty($data['status'])) {
            $query->where('status', $data['status']);
        }
        if (!empty($data['invoice_id'])) {
            $query->where('invoice_id', $data['invoice_id']);
        }
        if (!empty($data['reference_code'])) {
            $query->where('reference_code', $data['reference_code']);
        }
        if (!empty($data['tax_id'])) {
            $query->where('tax_id', $data['tax_id']);
        }

        return self::paginate($query);
    }
}

<?php

namespace App\Repositories\Invoice;

use App\Models\InvoiceNumber;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Invoice\Interface\InvoiceNumberRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class InvoiceNumberRepository extends BaseRepository implements InvoiceNumberRepositoryInterface
{
    public string $model = InvoiceNumber::class;

    /**
     * @throws BindingResolutionException
     */
    public function adminIndex(array $data): LengthAwarePaginator
    {
        $query = self::newQuery();
        if (!empty($data['search'])) {
            $query->where(function (Builder $query) use ($data) {
                $query->where('invoice_id', 'LIKE', '%%' . $data['search'] . '%')
                    ->orWhere('fiscal_year', 'LIKE', '%%' . $data['search'] . '%')
                    ->orWhere('invoice_number', 'LIKE', '%%' . $data['search'] . '%');
            });
        }
        if (!empty($data['type'])) {
            $query->where('type', $data['type']);
        }
        if (!empty($data['status'])) {
            $query->where('status', $data['status']);
        }
        if (!empty($data['invoice_id'])) {
            $query->where('invoice_id', $data['invoice_id']);
        }

        return self::paginate($query);
    }
}

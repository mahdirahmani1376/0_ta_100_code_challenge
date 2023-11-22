<?php

namespace App\Repositories\Invoice;

use App\Models\Invoice;
use App\Models\Item;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Invoice\Interface\ItemRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ItemRepository extends BaseRepository implements ItemRepositoryInterface
{
    public string $model = Item::class;

    public function indexItemByCriteria(array $criteria, int $limit = 100): Collection
    {
        if (empty($criteria['search'])) {
            return collect();
        }

        $query = self::newQuery();
        $query->where('description', 'LIKE', "%" . $criteria['search'] . "%");

        // if profile_id is provided, search only in items of a specific client's invoices
        if (!empty($criteria['profile_id'])) {
            $query->whereHas('invoice', function (Builder $builder) use ($criteria) {
                $builder->where('profile_id', $criteria['profile_id']);
            });
        }

        $query->limit($limit);

        return $query->get();
    }

    public function sumAmountByInvoice(Invoice $invoice): int
    {
        return $this->newQuery()
            ->where('invoice_id', $invoice->getKey())
            ->sum('amount');
    }

    public function indexByInvoices(array $invoiceIds): Collection
    {
        return self::newQuery()
            ->whereIn('invoice_id', $invoiceIds)
            ->get();
    }

    public function reAssignItemsToInvoice(Invoice $invoice, array $itemIds): int
    {
        return self::newQuery()
            ->whereIn('id', $itemIds)
            ->update(['invoice_id' => $invoice->getKey()]);
    }

    public function findAddCreditItem(Invoice $invoice): ?Item
    {
        return self::newQuery()
            ->where('invoice_id', $invoice->getKey())
            ->whereIn('status', [
                Item::TYPE_ADD_CLIENT_CREDIT,
                Item::TYPE_ADD_CLOUD_CREDIT,
            ])
            ->first();
    }
}

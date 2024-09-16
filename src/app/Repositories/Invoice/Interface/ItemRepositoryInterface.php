<?php

namespace App\Repositories\Invoice\Interface;

use App\Models\Invoice;
use App\Models\Item;
use App\Repositories\Base\Interface\EloquentRepositoryInterface;
use Illuminate\Support\Collection;

interface ItemRepositoryInterface extends EloquentRepositoryInterface
{
    public function indexItemByCriteria(array $criteria, int $limit = 100): Collection;

    public function sumAmountByInvoice(Invoice $invoice): float;

    public function indexByInvoices(array $invoiceIds): Collection;

    public function reAssignItemsToInvoice(Invoice $invoice, array $itemIds): int;

    public function findAddCreditItem(Invoice $invoice): ?Item;

    public function sumItemsAmount(mixed $item_ids);
}

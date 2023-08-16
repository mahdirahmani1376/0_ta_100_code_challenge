<?php

namespace App\Repositories\Invoice\Interface;

use App\Models\Invoice;
use App\Repositories\Base\Interface\EloquentRepositoryInterface;
use Illuminate\Support\Collection;

interface ItemRepositoryInterface extends EloquentRepositoryInterface
{
    public function indexItemByCriteria(array $criteria, int $limit = 100): Collection;

    public function sumAmountByInvoice(Invoice $invoice): int;

    public function indexByInvoices(array $invoiceIds): Collection;
    public function reAssignItemsToInvoice(Invoice $invoice, array $itemIds): int;
}

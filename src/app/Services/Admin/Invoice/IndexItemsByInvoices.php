<?php

namespace App\Services\Admin\Invoice;

use App\Repositories\Invoice\Interface\ItemRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class IndexItemsByInvoices
{
    public function __construct(private readonly ItemRepositoryInterface $itemRepository)
    {
    }

    public function __invoke(Collection $invoices)
    {
        return $this->itemRepository->indexByInvoices($invoices->pluck('id')->toArray());
    }
}

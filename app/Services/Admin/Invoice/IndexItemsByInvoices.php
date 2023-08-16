<?php

namespace App\Services\Admin\Invoice;

use App\Repositories\Invoice\Interface\ItemRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class IndexItemsByInvoices
{
    private ItemRepositoryInterface $itemRepository;

    public function __construct(ItemRepositoryInterface $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    public function __invoke(Collection $invoices)
    {
        return $this->itemRepository->indexByInvoices($invoices->pluck('id')->toArray());
    }
}

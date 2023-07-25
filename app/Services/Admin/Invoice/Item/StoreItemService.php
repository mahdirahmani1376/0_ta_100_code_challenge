<?php

namespace App\Services\Admin\Invoice\Item;

use App\Models\Invoice;
use App\Repositories\Invoice\Interface\ItemRepositoryInterface;

class StoreItemService
{
    private ItemRepositoryInterface $itemRepository;

    public function __construct(ItemRepositoryInterface $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    public function __invoke(Invoice $invoice, array $data)
    {
        return $this->itemRepository->create(
            array_merge($data, ['invoice_id' => $invoice->id,])
        );
    }
}

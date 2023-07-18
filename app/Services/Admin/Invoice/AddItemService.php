<?php

namespace App\Services\Admin\Invoice;

use App\Models\Invoice;
use App\Models\Item;
use App\Repositories\Invoice\Interface\ItemRepositoryInterface;

class AddItemService
{
    private ItemRepositoryInterface $itemRepository;

    public function __construct(ItemRepositoryInterface $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    public function __invoke(Invoice $invoice, array $data): Item
    {
        return $this->itemRepository->create(
            [
                'description' => $data['description'],
                'amount' => $data['amount'],
                'invoice_id' => $invoice->getKey(),
            ],
            ['description', 'amount', 'invoice_id',]
        );
    }
}

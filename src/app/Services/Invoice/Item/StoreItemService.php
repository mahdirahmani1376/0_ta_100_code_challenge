<?php

namespace App\Services\Invoice\Item;

use App\Models\Invoice;
use App\Repositories\Invoice\Interface\ItemRepositoryInterface;

class StoreItemService
{
    public function __construct(private readonly ItemRepositoryInterface $itemRepository)
    {
    }

    public function __invoke(Invoice $invoice, array $data)
    {
        $data['amount'] = (int)$data['amount'];
        return $this->itemRepository->create(
            array_merge($data, ['invoice_id' => $invoice->id,])
        );
    }
}

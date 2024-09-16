<?php

namespace App\Services\Invoice\Item;

use App\Exceptions\SystemException\ItemAmountInvalidException;
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
        if ($data['amount'] < 100) {
            \Log::warning("Item with amount less than 100 rials detected", [
                'traceback' => debug_backtrace(2)
            ]);
            throw ItemAmountInvalidException::make();
        }
        return $this->itemRepository->create(
            array_merge($data, ['invoice_id' => $invoice->id,])
        );
    }
}

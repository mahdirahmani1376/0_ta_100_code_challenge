<?php

use App\Exceptions\SystemException\InvoiceLockedAndAlreadyImportedToRahkaranException;
use App\Models\Invoice;

if (!function_exists('get_paginate_params')) {
    function get_paginate_params(): array
    {
        $perPage = \request()->get('perPage');
        $page = \request()->get('page');

        if (empty($perPage) || is_array($perPage) || is_object($perPage) || (int)$perPage < 0 || (int)$perPage > 200) {
            $perPage = 10;
        }
        if (empty($page) || is_array($page) || is_object($page) || ((int)$page < 0)) {
            $page = 1;
        }

        return ['perPage' => (int)$perPage, 'page' => (int)$page];
    }
}

if (!function_exists("get_sortable_items")) {
    /**
     * @param array $items
     * @return array
     */
    function get_sortable_items(array $items): array
    {
        return array_unique(array_merge($items, ['created_at', 'updated_at', 'id']));
    }
}

if (!function_exists("check_rahkaran")) {
    /**
     * @throws InvoiceLockedAndAlreadyImportedToRahkaranException
     */
    function check_rahkaran(Invoice $invoice): void
    {
        if ($invoice->rahkaran_id && $invoice->balance == 0 && in_array($invoice->status, [
                Invoice::STATUS_REFUNDED,
                Invoice::STATUS_PAID,
            ])) {
            throw InvoiceLockedAndAlreadyImportedToRahkaranException::make($invoice->getKey());
        }
    }
}

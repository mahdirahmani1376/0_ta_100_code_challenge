<?php

namespace App\Actions\Invoice;

use DB;

class GetInvoicesByItemTypeAction
{
    public function __invoke(array $data)
    {
        $query = "SELECT inv.*,i.invoiceable_type,i.invoiceable_id FROM invoices AS inv INNER JOIN items i ON inv.id = i.invoice_id WHERE 1=1";

        if (!empty($data['profile_id'])) {
            $query .= " AND inv.profile_id = {$data['profile_id']}";
        }

        if (!empty($data['status'])) {
            foreach ($data['status'] as $i => $status) {
                $data['status'][$i] = "'$status'";
            }
            $status = implode(',', $data['status']);
            $query .= " AND inv.status in ($status)";
        }

        if (!empty($data['invoiceable_types'])) {
            foreach ($data['invoiceable_types'] as $i => $type) {
                $data['invoiceable_types'][$i] = "'$type'";
            }
            $types = implode(',', $data['invoiceable_types']);
            $query .= " AND i.invoiceable_type in ($types)";
        }

        if (!empty($data['invoiceable_ids'])) {
            foreach ($data['invoiceable_ids'] as $i => $type) {
                $data['invoiceable_ids'][$i] = "'$type'";
            }
            $ids = implode(',', $data['invoiceable_ids']);
            $query .= " AND i.invoiceable_id in ($ids)";
        }

        $limit = $data['limit'] ?? 100;

        $query .= " ORDER BY inv.id DESC LIMIT $limit";

        return DB::select($query);
    }
}

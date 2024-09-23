<?php

namespace App\Actions\Invoice;

use DB;

class GetInvoicesByItemTypeAction
{
    public function __invoke(array $data)
    {
        $query = "
    SELECT * FROM invoices AS inv
    INNER JOIN items i ON inv.id = i.invoice_id WHERE 1=1";

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
            $status = implode(',', $data['invoiceable_types']);
            $query .= " AND i.invoiceable_type in ($status)";
        }

        return DB::select($query);
    }
}

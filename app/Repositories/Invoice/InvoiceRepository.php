<?php

namespace App\Repositories\Invoice;

use App\Common\Repository\BaseRepository;
use App\Models\Invoice;
use App\Repositories\Invoice\Interface\InvoiceInterface;

class InvoiceRepository extends BaseRepository implements InvoiceInterface
{
    public string $model = Invoice::class;
}

<?php

namespace App\Repositories\Invoice;

use App\Models\Invoice;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Invoice\Interface\InvoiceInterface;

class InvoiceRepository extends BaseRepository implements InvoiceInterface
{
    public string $model = Invoice::class;
}

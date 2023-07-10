<?php

namespace App\Repositories\Invoice;

use App\Models\InvoiceNumber;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Invoice\Interface\InvoiceInterface;

class InvoiceNumberRepository extends BaseRepository implements InvoiceInterface
{
    public string $model = InvoiceNumber::class;
}

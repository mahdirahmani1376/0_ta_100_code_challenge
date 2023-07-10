<?php

namespace App\Repositories\Invoice;

use App\Common\Repository\BaseRepository;
use App\Models\InvoiceNumber;
use App\Repositories\Invoice\Interface\InvoiceInterface;

class InvoiceNumberRepository extends BaseRepository implements InvoiceInterface
{
    public string $model = InvoiceNumber::class;
}
